<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class FakeCallback implements CallbackInterface
{
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callback)
    {
        $paymentTransaction->setStatus(PaymentTransaction::STATUS_APPROVED);
        return $callback;
    }
}