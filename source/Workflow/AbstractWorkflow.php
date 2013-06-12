<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Queries\QueryFactoryInterface;

/**
 * Abstract workflow
 */
abstract class AbstractWorkflow implements WorkflowInterface
{
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