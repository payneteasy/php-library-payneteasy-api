<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

class CustomerReturnCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'payment.paynetId'),
        array('merchant_order', 'payment.clientId'),
        array('client_orderid', 'payment.clientId'),
        array('status',          null),
        array('control',         null)
    );

    /**
     * {@inheritdoc}
     */
    protected function validateCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        if (!$paymentTransaction->isProcessing())
        {
            throw new ValidationException("Only processing payment transaction can be processed");
        }

        parent::validateCallback($paymentTransaction, $callbackResponse);
    }
}