<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\Utils\String;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;
use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

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
     * @var PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface
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
    public function processPayment(PaymentInterface $payment, array $callbackData = array())
    {
        switch($payment->getProcessingStage())
        {
            case null:
            {
                $response = $this->initializeProcessing($payment);
                break;
            }
            case PaymentInterface::STAGE_CREATED:
            {
                $response = $this->updateStatus($payment);
                break;
            }
            case PaymentInterface::STAGE_REDIRECTED:
            {
                if(empty($callbackData))
                {
                    throw new RuntimeException("Data parameter can not be empty " .
                                               "for transport stage '{$payment->getProcessingStage()}'");
                }

                $response = $this->processCallback($payment, $callbackData);
                break;
            }
            case PaymentInterface::STAGE_FINISHED:
            {
                throw new RuntimeException('Payment has been completed');
            }
            default:
            {
                throw new RuntimeException("Undefined payment transport stage: '{$payment->getProcessingStage()}'");
            }
        }

        $this->setNeededAction($response);

        return $response;
    }

    /**
     * Executes initial API method  query
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment        Payment for processing
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                                Query response
     */
    protected function initializeProcessing(PaymentInterface $payment)
    {
        return $this->executeQuery($this->initialApiMethod, $payment);
    }

    /**
     * Executes status query
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment        Payment for processing
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                                Query response
     */
    protected function updateStatus(PaymentInterface $payment)
    {
        return $this->executeQuery('status', $payment);
    }

    /**
     * Sets action needed after call to workflow
     *
     * @see Response::setNeededAction()
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response                $response       Query response
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
     * @param       array       $callbackData       Callback data
     *
     * @return      Response                        Callback object
     */
    protected function processCallback(PaymentInterface $payment, array $callbackData)
    {
        $callback = new CallbackResponse($callbackData);

        $this->callbackFactory
            ->getCallback($callback, $this->queryConfig)
            ->processCallback($payment, $callback);

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
     * @param       string                                                      $queryName          API method name
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment            Payment
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                                    Gateway response
     */
    protected function executeQuery($queryName, PaymentInterface $payment)
    {
        $query = $this->queryFactory->getQuery($queryName, $this->queryConfig);

        $request    = $query->createRequest($payment);
        $response   = $this->gatewayClient->makeRequest($request);

        $query->processResponse($payment, $response);

        return $response;
    }
}