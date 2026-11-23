<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use PaynetEasy\PaynetEasyApi\Util\PropertyAccessor;

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

        // The customer return carries the amount (but no currency). It is compared
        // when present, like in the server callback: a return for another amount,
        // e.g. after the order was changed, does not finish the payment.
        if (isset($callbackResponse['amount']) && trim((string) $callbackResponse['amount']) !== '')
        {
            $amount = PropertyAccessor::getValue($paymentTransaction, 'payment.amount', false);

            if (!static::isEqualValue('payment.amount', $amount, $callbackResponse['amount']))
            {
                throw new ValidationException('CallbackResponse amount does not equal the payment amount');
            }
        }
    }
}