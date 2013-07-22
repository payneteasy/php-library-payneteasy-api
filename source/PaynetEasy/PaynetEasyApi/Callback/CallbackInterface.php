<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Payment
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment               $payment                Payment for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse        $callbackResponse       Paynet callback
     */
    public function processCallback(Payment $payment, CallbackResponse $callbackResponse);
}