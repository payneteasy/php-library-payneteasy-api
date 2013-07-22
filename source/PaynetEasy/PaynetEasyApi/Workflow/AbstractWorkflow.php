<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\Utils\String;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
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
     * @param       string                          $initialApiMethod       Initial API method
     * @param       GatewayClientInterface          $gatewayClient          Client for API gateway
     * @param       QueryFactoryInterface           $queryFactory           Factory for API qieries
     * @param       CallbackFactoryInterface        $callbackFactory        Factory for API callbacks
     */
    public function __construct(                            $initialApiMethod,
                                GatewayClientInterface      $gatewayClient,
                                QueryFactoryInterface       $queryFactory,
                                CallbackFactoryInterface    $callbackFactory)
    {
        $this->initialApiMethod = $initialApiMethod;
        $this->gatewayClient    = $gatewayClient;
        $this->queryFactory     = $queryFactory;
        $this->callbackFactory  = $callbackFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function processPayment(Payment $payment, array $callbackData = array())
    {
        switch($payment->getProcessingStage())
        {
            case null:
            {
                $response = $this->initializeProcessing($payment);
                break;
            }
            case Payment::STAGE_CREATED:
            {
                $response = $this->updateStatus($payment);
                break;
            }
            case Payment::STAGE_REDIRECTED:
            {
                if(empty($callbackData))
                {
                    throw new RuntimeException("Data parameter can not be empty " .
                                               "for transport stage '{$payment->getProcessingStage()}'");
                }

                $response = $this->processCallback($payment, $callbackData);
                break;
            }
            case Payment::STAGE_FINISHED:
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
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment        Payment for processing
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                                Query response
     */
    protected function initializeProcessing(Payment $payment)
    {
        return $this->executeQuery($this->initialApiMethod, $payment);
    }

    /**
     * Executes status query
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment        Payment for processing
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                                Query response
     */
    protected function updateStatus(Payment $payment)
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
    protected function processCallback(Payment $payment, array $callbackData)
    {
        $callback = new CallbackResponse($callbackData);

        $this->callbackFactory
            ->getCallback($callback)
            ->processCallback($payment, $callback);

        return $callback;
    }

    /**
     * Creates API Query object by their API method name
     * and executes API method request
     *
     * @param       string                                             $queryName          API method name
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment            Payment
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                           Gateway response
     */
    protected function executeQuery($queryName, Payment $payment)
    {
        $query = $this->queryFactory->getQuery($queryName);

        $request    = $query->createRequest($payment);
        $response   = $this->gatewayClient->makeRequest($request);

        $query->processResponse($payment, $response);

        return $response;
    }
}