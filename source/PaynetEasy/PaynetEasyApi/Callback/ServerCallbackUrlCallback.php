<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

class ServerCallbackUrlCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        Payment::STATUS_PROCESSING,
        Payment::STATUS_APPROVED,
        Payment::STATUS_DECLINED,
        Payment::STATUS_FILTERED,
        Payment::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'paynetPaymentId'),
        array('merchant_order', 'clientPaymentId'),
        array('client_orderid', 'clientPaymentId'),
        array('amount',         'amount'),
        array('status',          null),
        array('type',            null),
        array('control',         null)
    );
}
