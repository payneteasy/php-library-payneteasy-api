<?php

namespace PaynetEasy\PaynetEasyApi;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\Request;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClient;
use PaynetEasy\PaynetEasyApi\Query\QueryFactory;
use PaynetEasy\PaynetEasyApi\Workflow\WorkflowFactory;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactory;

use RuntimeException;
use Exception;

class OrderProcessor
{
    /**
     * Order changed and should be saved
     */
    const HANDLER_SAVE_ORDER        = 'save_order';

    /**
     * Order status not changed and should be updated
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
     * Order processing ended
     */
    const HANDLER_FINISH_PROCESSING  = 'finish_processing';

    /**
     * Allowed handlers list
     *
     * @var array
     */
    static protected $allowedHandlers = array
    (
        self::HANDLER_SAVE_ORDER,
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
     * Full url to Paynet API gateway
     *
     * @var string
     */
    protected $gatewayUrl;

    /**
     * Handlers for processing actions
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * @param       string      $gatewayUrl     Full url to Paynet API gateway
     */
    public function __construct($gatewayUrl)
    {
        $this->gatewayUrl = $gatewayUrl;
    }

    /**
     * Executes payment workflow
     *
     * @param       string              $workflowName           Payment workflow name
     * @param       array               $workflowConfig         Payment workflow config
     * @param       OrderInterface      $order                  Order for processing
     * @param       array               $callbackData           Paynet callback data (optional)
     */
    public function executeWorkflow(                $workflowName,
                                    array           $workflowConfig,
                                    OrderInterface  $order,
                                    array           $callbackData       = array())
    {
        // prevent double processing for ended order
        if ($order->getTransportStage() == OrderInterface::STAGE_FINISHED)
        {
            $this->callHandler(self::HANDLER_FINISH_PROCESSING, $order);
            return;
        }

        try
        {
            $response = $this->getWorkflow($workflowName, $workflowConfig)
                            ->processOrder($order, $callbackData);
        }
        catch (Exception $e)
        {
            $order->addError($e);
            $this->callHandler(self::HANDLER_SAVE_ORDER, $order);
            throw $e;
        }

        $this->callHandler(self::HANDLER_SAVE_ORDER, $order, $response);

        // no action needed if order is ended
        if ($order->getTransportStage() == OrderInterface::STAGE_FINISHED)
        {
            $this->callHandler(self::HANDLER_FINISH_PROCESSING, $order, $response);
            return;
        }

        switch ($response->getNeededAction())
        {
            case Response::NEEDED_STATUS_UPDATE:
                $this->callHandler(self::HANDLER_STATUS_UPDATE,    $order, $response);
            break;
            case Response::NEEDED_SHOW_HTML:
                $this->callHandler(self::HANDLER_SHOW_HTML,         $order, $response);
            break;
            case Response::NEEDED_REDIRECT:
                $this->callHandler(self::HANDLER_REDIRECT,     $order, $response);
            break;
        }
    }

    /**
     * Executes payment API query
     *
     * @param       string              $queryName              Payment API query name
     * @param       array               $queryConfig            Payment API query config
     * @param       OrderInterface      $order                  Order for processing
     *
     * @return      Response                                    Current workflow query response
     */
    public function executeQuery($queryName, array $queryConfig, OrderInterface $order)
    {
        $query      = $this->getQuery($queryName, $queryConfig);
        $request    = $query->createRequest($order);

        try
        {
            $response   = $this->makeRequest($request);
        }
        catch (Exception $e)
        {
            $order->addError($e);
            throw $e;
        }

        try
        {
            $query->processResponse($order, $response);
        }
        catch (Exception $e)
        {
            $order->addError($e);
            throw $e;
        }

        return $response;
    }

    /**
     * Executes payment gateway callback processor
     *
     * @param       array               $callbackData           Callback data from payment gateway
     * @param       array               $callbackConfig         Callback processor config
     * @param       OrderInterface      $order                  Order for processing
     *
     * @return      CallbackResponse                            Validated payment gateway callback
     */
    public function executeCallback(array $callbackData, array $callbackConfig, OrderInterface $order)
    {
        $callbackResponse   = new CallbackResponse($callbackData);

        try
        {
            $this->getCallback($callbackResponse, $callbackConfig)
                 ->processCallback($order, $callbackResponse);
        }
        catch (Exception $e)
        {
            $order->addError($e);
            throw $e;
        }

        return $callbackResponse;
    }

    /**
     * Get workflow by their name.
     * Usually it is name of first workflow API method query.
     *
     * @param       string      $workflowName                               Workflow name
     * @param       array       $workflowConfig                             Workflow configuration
     *
     * @return      \PaynetEasy\PaynetEasyApi\Workflow\WorkflowInterface           Workflow for payment processing
     */
    public function getWorkflow($workflowName, $workflowConfig)
    {
        return $this->getWorkflowFactory()
                    ->getWorkflow($workflowName, $workflowConfig);
    }

    /**
     * Create API query object by API query method
     *
     * @param       string              $apiQueryName                       API query method
     * @param       array               $apiQueryConfig                     API query config
     *
     * @return      \PaynetEasy\PaynetEasyApi\Query\QueryInterface                 API query object
     */
    public function getQuery($apiQueryName, $apiQueryConfig)
    {
        return $this->getQueryFactory()
                    ->getQuery($apiQueryName, $apiQueryConfig);
    }

    /**
     * Create API callback processor by callback response
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse       $callbackResponse       Callback response
     * @param       array                                               $callbackConfig         Config for callback processor
     *
     * @return      \PaynetEasy\PaynetEasyApi\Callback\CallbackInterface                               Callback processor
     */
    public function getCallback(CallbackResponse $callbackResponse, array $callbackConfig)
    {
        return $this->getCallbackFactory()
                    ->getCallback($callbackResponse, $callbackConfig);
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
     * Handler receives two parameters: OrderInterface and Response.
     *
     * @see OrderProcessor::callHandler()
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
     * @see OrderProcessor::setHandler()
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
            $this->gatewayClient = new GatewayClient($this->gatewayUrl);
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
     * Handler callback receives two parameters: OrderInterface and Response (optional)
     *
     * @param       string                                          $handlerName        Handler name
     * @param       \PaynetEasy\PaynetEasyApi\OrderData\OrderInterface     $order              Order
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response           $response           Gateway response
     *
     * @return      self
     */
    protected function callHandler($handlerName, OrderInterface $order, Response $response = null)
    {
        $this->checkHandlerName($handlerName);

        if ($this->hasHandler($handlerName))
        {
            call_user_func($this->handlers[$handlerName], $order, $response);
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