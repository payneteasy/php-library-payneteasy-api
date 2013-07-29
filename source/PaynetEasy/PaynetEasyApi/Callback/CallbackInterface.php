<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Payment transaction
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment for update
     * @param       CallbackResponse        $callbackResponse       Paynet callback
     */
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse);
}