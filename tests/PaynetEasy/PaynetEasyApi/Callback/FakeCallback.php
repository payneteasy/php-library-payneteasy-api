<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class FakeCallback implements CallbackInterface
{
    public function processCallback(OrderInterface $order, CallbackResponse $callback)
    {
        return $callback;
    }
}