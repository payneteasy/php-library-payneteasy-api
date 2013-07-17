<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;

class RedirectUrlCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        PaymentInterface::STATUS_APPROVED,
        PaymentInterface::STATUS_DECLINED,
        PaymentInterface::STATUS_FILTERED,
        PaymentInterface::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'paynetPaymentId'),
        array('merchant_order', 'clientPaymentId'),
        array('client_orderid', 'clientPaymentId'),
        array('status',          null),
        array('control',         null)
    );
}