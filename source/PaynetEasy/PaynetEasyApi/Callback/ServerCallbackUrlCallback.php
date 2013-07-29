<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class ServerCallbackUrlCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        PaymentTransaction::STATUS_PROCESSING,
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
        array('amount',         'payment.amount'),
        array('status',          null),
        array('type',            null),
        array('control',         null)
    );
}
