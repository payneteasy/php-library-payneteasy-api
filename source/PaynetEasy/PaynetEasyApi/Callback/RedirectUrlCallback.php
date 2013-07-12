<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;

class RedirectUrlCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        OrderInterface::STATUS_APPROVED,
        OrderInterface::STATUS_DECLINED,
        OrderInterface::STATUS_FILTERED,
        OrderInterface::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'paynetOrderId'),
        array('merchant_order', 'clientOrderId'),
        array('client_orderid', 'clientOrderId'),
        array('status',          null),
        array('control',         null)
    );
}