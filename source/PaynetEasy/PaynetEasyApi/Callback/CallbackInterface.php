<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Payment
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment                Payment for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse        $callbackResponse       Paynet callback
     */
    public function processCallback(PaymentInterface $payment, CallbackResponse $callbackResponse);
}