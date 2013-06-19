<?php

namespace PaynetEasy\Paynet;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;
use PaynetEasy\Paynet\Workflow\WorkflowFactoryInterface;
use PaynetEasy\Paynet\Callback\CallbackFactoryInterface;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Request;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\CallbackResponse;

use PaynetEasy\Paynet\Transport\GatewayClient;
use PaynetEasy\Paynet\Query\QueryFactory;
use PaynetEasy\Paynet\Workflow\WorkflowFactory;
use PaynetEasy\Paynet\Callback\CallbackFactory;

use RuntimeException;
use Exception;

class OrderProcessor
{
    /**
     * Order changed and should be saved
     */
    const EVENT_ORDER_CHANGED           = 'order_changed';

    /**
     * Order status not changed and should be updated
     */
    const EVENT_STATUS_NOT_CHANGED      = 'status_not_changed';

    /**
     * Html received and should be displayed
     */
    const EVENT_HTML_RECEIVED           = 'html_received';

    /**
     * Redirect url received, customer shoud be to it
     */
    const EVENT_REDIRECT_RECEIVED       = 'redirect_received';

    /**
     * Allowed events list
     *
     * @var array
     */
    static protected $allowedEvents = array
    (
        self::EVENT_ORDER_CHANGED,
        self::EVENT_STATUS_NOT_CHANGED,
        self::EVENT_HTML_RECEIVED,
        self::EVENT_REDIRECT_RECEIVED
    );

    /**
     * Paynet gateway client
     *
     * @var \PaynetEasy\Paynet\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API request queries factory
     *
     * @var \PaynetEasy\Paynet\Query\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * Payment workflow factory
     *
     * @var \PaynetEasy\Paynet\Workflow\WorkflowFactoryInterface
     */
    protected $workflowFactory;

    /**
     * API callbacks factory
     *
     * @var \PaynetEasy\Paynet\Callback\CallbackFactoryInterface
     */
    protected $callbackFactory;

    /**
     * Full url to Paynet API gateway
     *
     * @var string
     */
    protected $gatewayUrl;

    /**
     * Listeners for processing events
     *
     * @var array
     */
    protected $eventListeners = array();

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
     *
     * @return      Response                                    Current workflow query response
     */
    public function executeWorkflow(                $workflowName,
                                    array           $workflowConfig,
                                    OrderInterface  $order,
                                    array           $callbackData       = array())
    {
        try
        {
            $response = $this->getWorkflow($workflowName, $workflowConfig)
                            ->processOrder($order, $callbackData);
        }
        catch (Exception $e)
        {
            $order->addError($e);
            $this->fireEvent(self::EVENT_ORDER_CHANGED, $order);
            throw $e;
        }

        $this->fireEvent(self::EVENT_ORDER_CHANGED, $order, $response);

        switch ($response->getNeededAction())
        {
            case Response::NEEDED_STATUS_UPDATE:
                $this->fireEvent(self::EVENT_STATUS_NOT_CHANGED,    $order, $response);
            break;
            case Response::NEEDED_SHOW_HTML:
                $this->fireEvent(self::EVENT_HTML_RECEIVED,         $order, $response);
            break;
            case Response::NEEDED_REDIRECT:
                $this->fireEvent(self::EVENT_REDIRECT_RECEIVED,     $order, $response);
            break;
        }

        return $response;
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
            $this->fireEvent(self::EVENT_ORDER_CHANGED, $order);
            throw $e;
        }

        try
        {
            $query->processResponse($order, $response);
        }
        catch (Exception $e)
        {
            $order->addError($e);
        }
        // finally
        {
            $this->fireEvent(self::EVENT_ORDER_CHANGED, $order, $response);
            if (isset($e)) throw $e;
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
        }
        // finally
        {
            $this->fireEvent(self::EVENT_ORDER_CHANGED, $order, $callbackResponse);
            if (isset($e)) throw $e;
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
     * @return      \PaynetEasy\Paynet\Workflow\WorkflowInterface           Workflow for payment processing
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
     * @return      \PaynetEasy\Paynet\Query\QueryInterface                 API query object
     */
    public function getQuery($apiQueryName, $apiQueryConfig)
    {
        return $this->getQueryFactory()
                    ->getQuery($apiQueryName, $apiQueryConfig);
    }

    /**
     * Create API callback processor by callback response
     *
     * @param       \PaynetEasy\Paynet\Transport\CallbackResponse       $callbackResponse       Callback response
     * @param       array                                               $callbackConfig         Config for callback processor
     *
     * @return      \PaynetEasy\Paynet\Callback\CallbackInterface                               Callback processor
     */
    public function getCallback(CallbackResponse $callbackResponse, array $callbackConfig)
    {
        return $this->getCallbackFactory()
                    ->getCallback($callbackResponse, $callbackConfig);
    }

    /**
     * Make request to the Paynet gateway
     *
     * @param   \PaynetEasy\Paynet\Transport\Request    $request    Request data
     *
     * @return  \PaynetEasy\Paynet\Transport\Response               Response data
     */
    public function makeRequest(Request $request)
    {
        return $this->getGatewayClient()
                    ->makeRequest($request);
    }

    /**
     * Set listener for processing event.
     * Listener receives two parameters: OrderInterface and Response.
     *
     * @see OrderProcessor::fireEvent()
     *
     * @param       string          $eventName              Event name
     * @param       callable        $eventListener          Event listener
     *
     * @return      self
     */
    public function setEventListener($eventName, $eventListener)
    {
        $this->checkEventName($eventName);

        if (!is_callable($eventListener))
        {
            throw new RuntimeException("Event listener must be callable");
        }

        $this->eventListeners[$eventName] = $eventListener;

        return $this;
    }

    /**
     * Set events listeners. Listeners array must follow new format:
     * [<eventName>:string => <eventListener>:callable]
     *
     * @see OrderProcessor::setEventListener()
     *
     * @param       array       $eventListeners         Events listener
     *
     * @return      self
     */
    public function setEventListeners(array $eventListeners)
    {
        foreach ($eventListeners as $eventName => $eventListener)
        {
            $this->setEventListener($eventName, $eventListener);
        }

        return $this;
    }

    /**
     * Remove listener for procesing event
     *
     * @param       string          $eventName              Event name
     *
     * @return      self
     */
    public function removeEventListener($eventName)
    {
        $this->checkEventName($eventName);

        unset($this->eventListeners[$eventName]);

        return $this;
    }

    /**
     * Remove all event listeners
     *
     * @return     self
     */
    public function removeEventListeners()
    {
        $this->eventListeners = array();

        return $this;
    }

    /**
     * Set gateway client
     *
     * @param       \PaynetEasy\Paynet\Transport\GatewayClientInterface         $gatewayClient          Gateway client
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
     * @param       \PaynetEasy\Paynet\Query\QueryFactoryInterface            $queryFactory           Query factory
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
     * @param       \PaynetEasy\Paynet\Workflow\WorkflowFactoryInterface        $workflowFactory        Workflow factory
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
     * @param       \PaynetEasy\Paynet\Callback\CallbackFactoryInterface        $callbackFactory        Callback factory
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
     * @return      \PaynetEasy\Paynet\Transport\GatewayClientInterface         Gateway client
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
     * @return      \PaynetEasy\Paynet\Query\QueryFactoryInterface            Query factory
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
     * @return      \PaynetEasy\Paynet\Workflow\WorkflowFactoryInterface        Workflow factory
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
     * @return      \PaynetEasy\Paynet\Callback\CallbackFactoryInterface        Callback factory
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
     * Executes event listener.
     * Listener receives two parameters: OrderInterface and Response (optional)
     *
     * @param       string                                          $eventName      Event name
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface     $order          Order
     * @param       \PaynetEasy\Paynet\Transport\Response           $response       Gateway response
     *
     * @return      self
     */
    protected function fireEvent($eventName, OrderInterface $order, Response $response = null)
    {
        $this->checkEventName($eventName);

        if ($this->hasEventListener($eventName))
        {
            call_user_func($this->eventListeners[$eventName], $order, $response);
        }

        return $this;
    }

    /**
     * Check if event name is allowed
     *
     * @param       string      $eventName      Event name
     *
     * @throws      RuntimeException            Event name not allowed
     */
    protected function checkEventName($eventName)
    {
        if (!in_array($eventName, static::$allowedEvents))
        {
            throw new RuntimeException("Unknown event name: {$eventName}");
        }
    }

    /**
     * True if processor has event listener for given event name
     *
     * @param       string      $eventName      Event name
     *
     * @return      boolean
     */
    protected function hasEventListener($eventName)
    {
        if (!array_key_exists($eventName, $this->eventListeners))
        {
            return false;
        }

        return is_callable($this->eventListeners[$eventName]);
    }
}