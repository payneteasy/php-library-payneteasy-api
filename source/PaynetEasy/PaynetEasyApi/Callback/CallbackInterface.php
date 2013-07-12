<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\PaynetEasyApi\OrderData\OrderInterface         $order                  Order for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse       $callbackResponse       Paynet callback
     */
    public function processCallback(OrderInterface $order, CallbackResponse $callbackResponse);
}