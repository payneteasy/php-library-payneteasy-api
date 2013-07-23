<?php

namespace PaynetEasy\PaynetEasyApi;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Request;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClient;
use PaynetEasy\PaynetEasyApi\Query\QueryFactory;
use PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactory;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactory;

use RuntimeException;
use Exception;

class PaymentProcessor
{
    /**
     * Payment changed and should be saved
     */
    const HANDLER_SAVE_PAYMENT      = 'save_payment';

    /**
     * Payment status not changed and should be updated
     */
    const HANDLER_STATUS_UPDATE     = 'status_update';

    /**
     * Html received and should be displayed
     */
    const HANDLER_SHOW_HTML         = 'show_html';

    /**
     * Redirect url received, customer shoud be to it
     */
    const HANDLER_REDIRECT          = 'redirect';

    /**
     * Payment processing ended
     */
    const HANDLER_FINISH_PROCESSING = 'finish_processing';

    /**
     * Allowed handlers list
     *
     * @var array
     */
    static protected $allowedHandlers = array
    (
        self::HANDLER_SAVE_PAYMENT,
        self::HANDLER_STATUS_UPDATE,
        self::HANDLER_SHOW_HTML,
        self::HANDLER_REDIRECT,
        self::HANDLER_FINISH_PROCESSING
    );

    /**
     * Paynet gateway client
     *
     * @var \PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API request queries factory
     *
     * @var \PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * Payment workflow factory
     *
     * @var \PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactoryInterface
     */
    protected $workflowFactory;

    /**
     * API callbacks factory
     *
     * @var \PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface
     */
    protected $callbackFactory;

    /**
     * Handlers for processing actions
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * Executes payment workflow
     *
     * @param       string      $workflowName       Payment workflow name
     * @param       Payment     $payment            Payment for processing
     * @param       array       $callbackData       Paynet callback data (optional)
     */
    public function executeWorkflow($workflowName, Payment $payment, array $callbackData = array())
    {
        // prevent double processing for finished payment
        if ($payment->isFinished())
        {
            $this->callHandler(self::HANDLER_FINISH_PROCESSING, $payment);
            return;
        }

        try
        {
            $response = $this->getWorkflow($workflowName)
                            ->processPayment($payment, $callbackData);
        }
        catch (Exception $e)
        {
            $payment->addError($e);
            $this->callHandler(self::HANDLER_SAVE_PAYMENT, $payment);
            throw $e;
        }

        $this->callHandler(self::HANDLER_SAVE_PAYMENT, $payment, $response);

        // no action needed if payment is finished
        if ($payment->isFinished())
        {
            $this->callHandler(self::HANDLER_FINISH_PROCESSING, $payment, $response);
            return;
        }

        switch ($response->getNeededAction())
        {
            case Response::NEEDED_STATUS_UPDATE:
                $this->callHandler(self::HANDLER_STATUS_UPDATE,    $payment, $response);
            break;
            case Response::NEEDED_SHOW_HTML:
                $this->callHandler(self::HANDLER_SHOW_HTML,         $payment, $response);
            break;
            case Response::NEEDED_REDIRECT:
                $this->callHandler(self::HANDLER_REDIRECT,     $payment, $response);
            break;
        }
    }

    /**
     * Executes payment API query
     *
     * @param       string      $queryName      Payment API query name
     * @param       Payment     $payment        Payment for processing
     *
     * @return      Response                    Current workflow query response
     */
    public function executeQuery($queryName, Payment $payment)
    {
        $query      = $this->getQuery($queryName);
        $request    = $query->createRequest($payment);

        try
        {
            $response   = $this->makeRequest($request);
        }
        catch (Exception $e)
        {
            $payment->addError($e);
            throw $e;
        }

        try
        {
            $query->processResponse($payment, $response);
        }
        catch (Exception $e)
        {
            $payment->addError($e);
            throw $e;
        }

        return $response;
    }

    /**
     * Executes payment gateway callback processor
     *
     * @param       CallbackResponse        $callbackResponse       Callback object with data from payment gateway
     * @param       Payment                 $payment                Payment for processing
     *
     * @return      CallbackResponse                                Validated payment gateway callback
     */
    public function executeCallback(CallbackResponse $callbackResponse, Payment $payment)
    {
        try
        {
            $this->getCallback($callbackResponse)
                 ->processCallback($payment, $callbackResponse);
        }
        catch (Exception $e)
        {
            $payment->addError($e);
            throw $e;
        }

        return $callbackResponse;
    }

    /**
     * Get workflow by their name.
     * Usually it is name of first workflow API method query.
     *
     * @param       string      $workflowName                               Workflow name
     *
     * @return      \PaynetEasy\PaynetEasyApi\Workflow\WorkflowInterface    Workflow for payment processing
     */
    public function getWorkflow($workflowName)
    {
        return $this->getWorkflowFactory()
                    ->getWorkflow($workflowName);
    }

    /**
     * Create API query object by API query method
     *
     * @param       string              $apiQueryName                       API query method
     *
     * @return      \PaynetEasy\PaynetEasyApi\Query\QueryInterface          API query object
     */
    public function getQuery($apiQueryName)
    {
        return $this->getQueryFactory()
                    ->getQuery($apiQueryName);
    }

    /**
     * Create API callback processor by callback response
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse        $callbackResponse       Callback response
     *
     * @return      \PaynetEasy\PaynetEasyApi\Callback\CallbackInterface                                Callback processor
     */
    public function getCallback(CallbackResponse $callbackResponse)
    {
        return $this->getCallbackFactory()
                    ->getCallback($callbackResponse);
    }

    /**
     * Make request to the Paynet gateway
     *
     * @param   \PaynetEasy\PaynetEasyApi\Transport\Request    $request    Request data
     *
     * @return  \PaynetEasy\PaynetEasyApi\Transport\Response               Response data
     */
    public function makeRequest(Request $request)
    {
        return $this->getGatewayClient()
                    ->makeRequest($request);
    }

    /**
     * Set handler callback for processing action.
     * Handler receives two parameters: Payment and Response.
     *
     * @see PaymentProcessor::callHandler()
     *
     * @param       string          $handlerName            Handler name
     * @param       callable        $handlerCallback        Handler callbac
     *
     * @return      self
     */
    public function setHandler($handlerName, $handlerCallback)
    {
        $this->checkHandlerName($handlerName);

        if (!is_callable($handlerCallback))
        {
            throw new RuntimeException("Handler callback must be callable");
        }

        $this->handlers[$handlerName] = $handlerCallback;

        return $this;
    }

    /**
     * Set handlers. Handlers array must follow new format:
     * [<handlerName>:string => <handlerCallback>:callable]
     *
     * @see PaymentProcessor::setHandler()
     *
     * @param       array       $handlers         Handlers callbacks
     *
     * @return      self
     */
    public function setHandlers(array $handlers)
    {
        foreach ($handlers as $handlerName => $handlerCallback)
        {
            $this->setHandler($handlerName, $handlerCallback);
        }

        return $this;
    }

    /**
     * Remove handler for procesing action
     *
     * @param       string          $handlerName            Handler name
     *
     * @return      self
     */
    public function removeHandler($handlerName)
    {
        $this->checkHandlerName($handlerName);

        unset($this->handlers[$handlerName]);

        return $this;
    }

    /**
     * Remove all handlers
     *
     * @return     self
     */
    public function removeHandlers()
    {
        $this->handlers = array();

        return $this;
    }

    /**
     * Set gateway client
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface         $gatewayClient          Gateway client
     *
     * @return      self
     */
    public function setGatewayClient(GatewayClientInterface $gatewayClient)
    {
        $this->gatewayClient = $gatewayClient;

        return $this;
    }

    /**
     * Set query factory
     *
     * @param       \PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface              $queryFactory           Query factory
     *
     * @return      self
     */
    public function setQueryFactory(QueryFactoryInterface $queryFactory)
    {
        $this->queryFactory = $queryFactory;

        return $this;
    }

    /**
     * Set workflow factory
     *
     * @param       \PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactoryInterface        $workflowFactory        Workflow factory
     *
     * @return      self
     */
    public function setWorkflowFactory(WorkflowFactoryInterface $workflowFactory)
    {
        $this->workflowFactory = $workflowFactory;

        return $this;
    }

    /**
     * Set callback factory
     *
     * @param       \PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface        $callbackFactory        Callback factory
     *
     * @return      self
     */
    public function setCallbackFactory(CallbackFactoryInterface $callbackFactory)
    {
        $this->callbackFactory = $callbackFactory;

        return $this;
    }

    /**
     * Get getaway client
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface         Gateway client
     */
    public function getGatewayClient()
    {
        if (!is_object($this->gatewayClient))
        {
            $this->gatewayClient = new GatewayClient;
        }

        return $this->gatewayClient;
    }

    /**
     * Get query factory
     *
     * @return      \PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface              Query factory
     */
    public function getQueryFactory()
    {
        if (!is_object($this->queryFactory))
        {
            $this->queryFactory = new QueryFactory;
        }

        return $this->queryFactory;
    }

    /**
     * Get workflow factory
     *
     * @return      \PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactoryInterface        Workflow factory
     */
    public function getWorkflowFactory()
    {
        if (!is_object($this->workflowFactory))
        {
            $this->workflowFactory = new WorkflowFactory($this->getGatewayClient(),
                                                         $this->getQueryFactory(),
                                                         $this->getCallbackFactory());
        }

        return $this->workflowFactory;
    }

    /**
     * Get callback factory
     *
     * @return      \PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface        Callback factory
     */
    public function getCallbackFactory()
    {
        if (!is_object($this->callbackFactory))
        {
            $this->callbackFactory = new CallbackFactory;
        }

        return $this->callbackFactory;
    }

    /**
     * Executes handler callback.
     * Handler callback receives two parameters: Payment and Response (optional)
     *
     * @param       string                                                      $handlerName        Handler name
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment            Payment
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response                $response           Gateway response
     *
     * @return      self
     */
    protected function callHandler($handlerName, Payment $payment, Response $response = null)
    {
        $this->checkHandlerName($handlerName);

        if ($this->hasHandler($handlerName))
        {
            call_user_func($this->handlers[$handlerName], $payment, $response);
        }

        return $this;
    }

    /**
     * Check if handler name is allowed
     *
     * @param       string      $handlerName        Handler name
     *
     * @throws      RuntimeException                Handler name not allowed
     */
    protected function checkHandlerName($handlerName)
    {
        if (!in_array($handlerName, static::$allowedHandlers))
        {
            throw new RuntimeException("Unknown handler name: '{$handlerName}'");
        }
    }

    /**
     * True if processor has handler callback for given handler name
     *
     * @param       string      $handlerName        Handler name
     *
     * @return      boolean
     */
    protected function hasHandler($handlerName)
    {
        if (!array_key_exists($handlerName, $this->handlers))
        {
            return false;
        }

        return is_callable($this->handlers[$handlerName]);
    }
}