<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\CallbackResponse;

class FakeCallback implements CallbackInterface
{
    public function processCallback(OrderInterface $order, CallbackResponse $callback)
    {
        return $callback;
    }
}