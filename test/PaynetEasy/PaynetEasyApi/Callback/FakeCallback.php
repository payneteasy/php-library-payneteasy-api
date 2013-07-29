<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class FakeCallback implements CallbackInterface
{
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callback)
    {
        $paymentTransaction->setProcessingStage(PaymentTransaction::STAGE_FINISHED);
        return $callback;
    }
}