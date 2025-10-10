<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

class PaynetEasyCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'payment.paynetId'),
        array('merchant_order', 'payment.clientId'),
        array('client_orderid', 'payment.clientId'),
        array('amount',         'payment.amount'),
        array('status',          null),
        array('type',            null),
        array('control',         null)
    );

    /**
     * {@inheritdoc}
     */
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        $paymentTransaction->setProcessorType(PaymentTransaction::PROCESSOR_CALLBACK);
        $paymentTransaction->setProcessorName($callbackResponse->getType());

        parent::processCallback($paymentTransaction, $callbackResponse);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        if (!$paymentTransaction->isNew())
        {
            throw new ValidationException("Only new payment transaction can be processed");
        }

        parent::validateCallback($paymentTransaction, $callbackResponse);
    }
}
