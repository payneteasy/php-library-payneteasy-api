<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Queries\QueryFactoryInterface;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Callbacks\Redirect3D;

use PaynetEasy\Paynet\Exceptions\ConfigException;
use PaynetEasy\Paynet\Exceptions\PaynetException;

/**
 * Abstract workflow
 */
abstract class AbstractWorkflow implements WorkflowInterface
{
    /**
     * Initial Paynet API method
     *
     * @var string
     */
    protected $initialApiMethod;

    /**
     * Paynet gateway client
     *
     * @var \PaynetEasy\Paynet\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API request queries factory
     *
     * @var \PaynetEasy\Paynet\Queries\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * Config for API queries
     *
     * @var array
     */
    protected $queryConfig;

    /**
     * Constructor
     * @param       GatewayClientInterface        $gatewayClient
     */
    public function __construct(GatewayClientInterface  $gatewayClient,
                                QueryFactoryInterface   $queryFactory,
                                array                   $queryConfig  = array())
    {
        $this->gatewayClient = $gatewayClient;
        $this->queryFactory  = $queryFactory;
        $this->queryConfig   = $queryConfig;

        $this->setInitialApiMethod(get_called_class());
    }

    /**
     * Process Order with different state
     *
     * @param       PaynetEasy\Paynet\Data\OrderInterface   $order              Order for processing
     * @param       array                                   $callbackData       Paynet callback data
     *
     * @return      \PaynetEasy\Paynet\Transport\Response
     */
    public function processOrder(OrderInterface $order, array $callbackData = array())
    {
        switch($order->getState())
        {
            case OrderInterface::STATE_NULL:
            case OrderInterface::STATE_INIT:
            {
                return $this->initQuery($order);
            }
            case OrderInterface::STATE_PROCESSING:
            case OrderInterface::STATE_WAIT:
            {
                return $this->statusQuery($order);
            }
            case OrderInterface::STATE_REDIRECT:
            {
                if(empty($callbackData))
                {
                    throw new PaynetException('Data parameter can not be empty for state "' .
                                              OrderInterface::STATE_REDIRECT . '"');
                }

                return $this->redirectCallback($order, $callbackData);
            }
            case OrderInterface::STATE_END:
            {
                return null;
            }
            default:
            {
                throw new PaynetException('Undefined state = ' . $order->getState());
            }
        }
    }

    protected function initQuery(OrderInterface $order)
    {
        $order->setState(OrderInterface::STATE_PROCESSING);

        return $this->executeQuery($this->initialApiMethod, $order);
    }

    protected function statusQuery(OrderInterface $order)
    {
        return $this->executeQuery('status', $order);
    }

    /**
     * The method handles the callback after the 3D
     *
     * @param       array $data
     * @return      Response
     *
     * @throws      PaynetException
     */
    protected function redirectCallback(OrderInterface $order, $data)
    {
        $order->setState(OrderInterface::STATE_WAIT);

        $callback   = new Redirect3D($this->queryConfig);

        $request    = $callback->createRequest($order, $data);
        $response   = new Response($request->getArrayCopy());
        $callback->processResponse($order, $response);

        return $response;
    }

    /**
     * Set initial API query method. Workflow class name must follow next convention:
     *
     * (initial API query)              (workflow class name)
     * make-rebill              =>      MakeRebillWorkflow
     * sale                     =>      SaleWorkflow
     *
     * @param       string      $class          API query object class
     */
    protected function setInitialApiMethod($class)
    {
        if (!empty($this->initialApiMethod))
        {
            return;
        }

        $result = array();

        preg_match('#(?<=\\\\)\w+(?=Workflow)#i', $class, $result);

        if (empty($result))
        {
            throw new ConfigException('Initial API method name not found in class name');
        }

        $name_chunks = preg_split('/(?=[A-Z])/', $result[0], null, PREG_SPLIT_NO_EMPTY);

        $this->initialApiMethod    = strtolower(implode('-', $name_chunks));
    }

    /**
     * Creates API Query object by their API method name
     * and executes API method request
     *
     * @param       string                                      $queryName          API method name
     * @param       \PaynetEasy\Paynet\Data\OrderInterface      $order              Order
     *
     * @return      \PaynetEasy\Paynet\Transport\Response                           Gateway response
     */
    protected function executeQuery($queryName, OrderInterface $order)
    {
        $query = $this->queryFactory->getQuery($queryName, $this->queryConfig);

        $request    = $query->createRequest($order);
        $response   = $this->gatewayClient->makeRequest($request);

        $query->processResponse($order, $response);

        return $response;
    }
}