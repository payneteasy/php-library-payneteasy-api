<?php

namespace PaynetEasy\PaynetEasyApi\Query\Prototype;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

class PaymentQuery extends Query
{
    /**
     * Status for payment, when it is processing by this query
     *
     * @var string
     */
    static protected $paymentStatus;

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-response';

    public function createRequest(PaymentTransaction $paymentTransaction)
    {
        $request = parent::createRequest($paymentTransaction);

        $paymentTransaction->getPayment()->setStatus(static::$paymentStatus);
        $paymentTransaction->setProcessorType(PaymentTransaction::PROCESSOR_QUERY);
        $paymentTransaction->setProcessorName($this->apiMethod);
        $paymentTransaction->setStatus(PaymentTransaction::STATUS_PROCESSING);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function validatePaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        if ($paymentTransaction->getPayment()->hasProcessingTransaction())
        {
            throw new ValidationException('Payment can not has processing payment transaction');
        }

        if (!$paymentTransaction->isNew())
        {
            throw new ValidationException('Payment transaction must be new');
        }

        parent::validatePaymentTransaction($paymentTransaction);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateQueryDefinition()
    {
        parent::validateQueryDefinition();

        if (empty(static::$paymentStatus))
        {
            throw new RuntimeException('You must configure paymentStatus property');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::updatePaymentTransactionOnSuccess($paymentTransaction, $response);

        if ($response->isProcessing())
        {
            $response->setNeededAction(Response::NEEDED_STATUS_UPDATE);
        }
    }
}