<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;

class CustomerReturnCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        PaymentTransaction::STATUS_APPROVED,
        PaymentTransaction::STATUS_DECLINED,
        PaymentTransaction::STATUS_FILTERED,
        PaymentTransaction::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'payment.paynetPaymentId'),
        array('merchant_order', 'payment.clientPaymentId'),
        array('client_orderid', 'payment.clientPaymentId'),
        array('status',          null),
        array('control',         null)
    );
}