<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Utils\String;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;
use PaynetEasy\Paynet\Callback\CallbackFactoryInterface;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\CallbackResponse;

use RuntimeException;

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
     * @var \PaynetEasy\Paynet\Query\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * API callbacks factory
     *
     * @var PaynetEasy\Paynet\Callback\CallbackFactoryInterface
     */
    protected $callbackFactory;

    /**
     * Config for API queries
     *
     * @var array
     */
    protected $queryConfig;

    /**
     * @param       GatewayClientInterface          $gatewayClient          Client for API gateway
     * @param       QueryFactoryInterface           $queryFactory           Factory for API qieries
     * @param       CallbackFactoryInterface        $callbackFactory        Factory for API callbacks
     * @param       array                           $queryConfig            Config for queries
     */
    public function __construct(GatewayClientInterface      $gatewayClient,
                                QueryFactoryInterface       $queryFactory,
                                CallbackFactoryInterface    $callbackFactory,
                                array                       $queryConfig        = array())
    {
        $this->gatewayClient    = $gatewayClient;
        $this->queryFactory     = $queryFactory;
        $this->queryConfig      = $queryConfig;
        $this->callbackFactory  = $callbackFactory;

        $this->setInitialApiMethod(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function processOrder(OrderInterface $order, array $callbackData = array())
    {
        switch($order->getTransportStage())
        {
            case null:
            {
                $response = $this->initializeProcessing($order);
                break;
            }
            case OrderInterface::STAGE_CREATED:
            {
                $response = $this->updateStatus($order);
                break;
            }
            case OrderInterface::STAGE_REDIRECTED:
            {
                if(empty($callbackData))
                {
                    throw new RuntimeException("Data parameter can not be empty " .
                                               "for transport stage '{$order->getTransportStage()}'");
                }

                $response = $this->processCallback($order, $callbackData);
                break;
            }
            case OrderInterface::STAGE_ENDED:
            {
                throw new RuntimeException('Payment has been completed');
            }
            default:
            {
                throw new RuntimeException("Undefined order transport stage: '{$order->getTransportStage()}'");
            }
        }

        $this->setNeededAction($response);

        return $response;
    }

    /**
     * Executes initial API method  query
     *
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface      $order          Order for processing
     *
     * @return      \PaynetEasy\Paynet\Transport\Response                       Query response
     */
    protected function initializeProcessing(OrderInterface $order)
    {
        return $this->executeQuery($this->initialApiMethod, $order);
    }

    /**
     * Executes status query
     *
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface      $order          Order for processing
     *
     * @return      \PaynetEasy\Paynet\Transport\Response                       Query response
     */
    protected function updateStatus(OrderInterface $order)
    {
        return $this->executeQuery('status', $order);
    }

    /**
     * Sets action needed after call to workflow
     *
     * @see Response::setNeededAction()
     *
     * @param       \PaynetEasy\Paynet\Transport\Response       $response       Query response
     */
    protected function setNeededAction(Response $response)
    {
        if ($response->hasRedirectUrl())
        {
            $response->setNeededAction(Response::NEEDED_REDIRECT);
        }
        elseif ($response->hasHtml())
        {
            $response->setNeededAction(Response::NEEDED_SHOW_HTML);
        }
        elseif ($response->isProcessing())
        {
            $response->setNeededAction(Response::NEEDED_STATUS_UPDATE);
        }
    }

    /**
     * Handles the callback after the redirect to Paynet
     *
     * @param       array $data
     * @return      Response
     */
    protected function processCallback(OrderInterface $order, array $data)
    {
        $callback = new CallbackResponse($data);

        $this->callbackFactory
            ->getCallback($callback, $this->queryConfig)
            ->processCallback($order, $callback);

        return $callback;
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
            throw new RuntimeException('Initial API method name not found in class name');
        }

        $this->initialApiMethod    = String::uncamelize($result[0], '-');
    }

    /**
     * Creates API Query object by their API method name
     * and executes API method request
     *
     * @param       string                                      $queryName          API method name
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface      $order              Order
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