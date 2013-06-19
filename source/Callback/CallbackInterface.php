<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface         $order                  Order for update
     * @param       \PaynetEasy\Paynet\Transport\CallbackResponse       $callbackResponse       Paynet callback
     */
    public function processCallback(OrderInterface $order, CallbackResponse $callbackResponse);
}