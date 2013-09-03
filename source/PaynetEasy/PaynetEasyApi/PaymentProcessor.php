<?php

namespace PaynetEasy\PaynetEasyApi;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Request;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClient;
use PaynetEasy\PaynetEasyApi\Query\QueryFactory;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactory;

use RuntimeException;
use Exception;

class PaymentProcessor
{
    /**
     * Payment changed and should be saved
     */
    const HANDLER_SAVE_CHANGES      = 'save_payment';

    /**
     * Payment status not changed and should be updated
     */
    const HANDLER_STATUS_UPDATE     = 'status_update';

    /**
     * Html received and should be displayed
     */
    const HANDLER_SHOW_HTML         = 'show_html';

    /**
     * Redirect url received, customer should be to it
     */
    const HANDLER_REDIRECT          = 'redirect';

    /**
     * Payment processing ended
     */
    const HANDLER_FINISH_PROCESSING = 'finish_processing';

    /**
     * Exception handle needed
     */
    const HANDLER_CATCH_EXCEPTION   = 'catch_exception';

    /**
     * Allowed handlers list
     *
     * @var array
     */
    static protected $allowedHandlers = array
    (
        self::HANDLER_SAVE_CHANGES,
        self::HANDLER_STATUS_UPDATE,
        self::HANDLER_SHOW_HTML,
        self::HANDLER_REDIRECT,
        self::HANDLER_FINISH_PROCESSING,
        self::HANDLER_CATCH_EXCEPTION
    );

    /**
     * PaynetEasy gateway client
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
     * @see PaymentProcessor::setHandlers()
     *
     * @param       array       $handlers         Handlers callbacks
     */
    public function __construct(array $handlers = array())
    {
        $this->setHandlers($handlers);
    }

    /**
     * Executes payment API query
     *
     * @param       string                  $queryName              Payment API query name
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for processing
     *
     * @return      Response                                        Query response
     */
    public function executeQuery($queryName, PaymentTransaction $paymentTransaction)
    {
        $query = $this->getQuery($queryName);
        $response = null;

        try
        {
            $request  = $query->createRequest($paymentTransaction);
            $response = $this->makeRequest($request);
            $query->processResponse($paymentTransaction, $response);
        }
        catch (Exception $e)
        {
            $this->handleException($e, $paymentTransaction, $response);
            return;
        }

        $this->handleQueryResult($paymentTransaction, $response);

        return $response;
    }

    /**
     * Executes payment gateway processor for customer return from payment form or 3D-auth
     *
     * @param       CallbackResponse        $callbackResponse       Callback object with data from payment gateway
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for processing
     *
     * @return      CallbackResponse                                Validated payment gateway callback
     */
    public function processCustomerReturn(CallbackResponse $callbackResponse, PaymentTransaction $paymentTransaction)
    {
        $callbackResponse->setType('customer_return');

        return $this->processPaynetEasyCallback($callbackResponse, $paymentTransaction);
    }

    /**
     * Executes payment gateway processor for PaynetEasy payment callback
     *
     * @param       CallbackResponse        $callbackResponse       Callback object with data from payment gateway
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for processing
     *
     * @return      CallbackResponse                                Validated payment gateway callback
     */
    public function processPaynetEasyCallback(CallbackResponse $callbackResponse, PaymentTransaction $paymentTransaction)
    {
        try
        {
            $this->getCallback($callbackResponse->getType())
                 ->processCallback($paymentTransaction, $callbackResponse);
        }
        catch (Exception $e)
        {
            $this->handleException($e, $paymentTransaction, $callbackResponse);
            return;
        }

        $this->handleQueryResult($paymentTransaction, $callbackResponse);

        return $callbackResponse;
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
     * @param       string              $callbackType                       Callback response type
     *
     * @return      \PaynetEasy\PaynetEasyApi\Callback\CallbackInterface    Callback processor
     */
    public function getCallback($callbackType)
    {
        return $this->getCallbackFactory()
                    ->getCallback($callbackType);
    }

    /**
     * Make request to the PaynetEasy gateway
     *
     * @param   \PaynetEasy\PaynetEasyApi\Transport\Request     $request    Request data
     *
     * @return  \PaynetEasy\PaynetEasyApi\Transport\Response                Response data
     */
    public function makeRequest(Request $request)
    {
        return $this->getGatewayClient()
                    ->makeRequest($request);
    }

    /**
     * Set handler callback for processing action.
     *
     * @see PaymentProcessor::callHandler()
     *
     * @param       string          $handlerName            Handler name
     * @param       callable        $handlerCallback        Handler callback
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
     * Remove handler for processing action
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
     * @param       GatewayClientInterface      $gatewayClient      Gateway client
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
     * @param       QueryFactoryInterface       $queryFactory       Query factory
     *
     * @return      self
     */
    public function setQueryFactory(QueryFactoryInterface $queryFactory)
    {
        $this->queryFactory = $queryFactory;

        return $this;
    }

    /**
     * Set callback factory
     *
     * @param       CallbackFactoryInterface        $callbackFactory        Callback factory
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
     * @return      GatewayClientInterface      Gateway client
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
     * @return      QueryFactoryInterface       Query factory
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
     * Get callback factory
     *
     * @return      CallbackFactoryInterface        Callback factory
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
     * Handle query result.
     * Method calls handlers for:
     *  - self::HANDLER_SAVE_CHANGES            always
     *  - self::HANDLER_STATUS_UPDATE           if needed payment transaction status update
     *  - self::HANDLER_SHOW_HTML               if needed to show response html
     *  - self::HANDLER_REDIRECT                if needed to redirect to response URL
     *  - self::HANDLER_FINISH_PROCESSING       if not additional action needed
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       Response                $response               Query result
     */
    protected function handleQueryResult(PaymentTransaction $paymentTransaction, Response $response)
    {
        $this->callHandler(self::HANDLER_SAVE_CHANGES, $paymentTransaction, $response);

        if ($response->isRedirectNeeded())
        {
            $this->callHandler(self::HANDLER_REDIRECT, $response, $paymentTransaction);
        }
        elseif ($response->isShowHtmlNeeded())
        {
            $this->callHandler(self::HANDLER_SHOW_HTML, $response, $paymentTransaction);
        }
        elseif ($response->isStatusUpdateNeeded())
        {
            $this->callHandler(self::HANDLER_STATUS_UPDATE, $response, $paymentTransaction);
        }
        else
        {
            $this->callHandler(self::HANDLER_FINISH_PROCESSING, $paymentTransaction, $response);
        }
    }

    /**
     * Handle throwned exception. If configured self::HANDLER_CATCH_EXCEPTION, handler will be called,
     * if not - exception will be rethrowned.
     *
     * @param       Exception               $exception              Exception to handle
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       Response                $response               Response or CallbackResponse
     *
     * @throws      Exception                                       Rethrowned exception, if has not self::HANDLER_CATCH_EXCEPTION
     */
    protected function handleException(Exception $exception, PaymentTransaction $paymentTransaction, Response $response = null)
    {
        $this->callHandler(self::HANDLER_SAVE_CHANGES, $paymentTransaction, $response);

        if (!$this->hasHandler(self::HANDLER_CATCH_EXCEPTION))
        {
            throw $exception;
        }

        $this->callHandler(self::HANDLER_CATCH_EXCEPTION, $exception, $paymentTransaction, $response);
    }

    /**
     * Executes handler callback.
     * Method receives at least one parameter - handler name,
     * all other parameters will be passed to handler callback.
     *
     * @param       string      $handlerName        Handler name
     *
     * @return      self
     */
    protected function callHandler($handlerName)
    {
        $this->checkHandlerName($handlerName);

        $arguments = func_get_args();
        array_shift($arguments);

        if ($this->hasHandler($handlerName))
        {
            call_user_func_array($this->handlers[$handlerName], $arguments);
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
        return array_key_exists($handlerName, $this->handlers);
    }
}