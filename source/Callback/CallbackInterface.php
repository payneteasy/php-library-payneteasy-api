<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Callback;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface         $order          Order for update
     * @param       \PaynetEasy\Paynet\Transport\Callback               $callback       Paynet callback
     */
    public function processCallback(OrderInterface $order, Callback $callback);
}