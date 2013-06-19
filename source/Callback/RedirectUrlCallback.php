<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;

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
    static protected $allowedFields = array
    (
        'status'            => true,
        'orderid'           => true,
        'merchant_order'    => true,
        'client_orderid' 	=> true,
        'control'           => true,

        'descriptor'        => false,
        'error_message' 	=> false
    );
}