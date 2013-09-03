<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Payment transaction
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for update
     * @param       CallbackResponse        $callbackResponse       PaynetEasy callback
     *
     * @return      CallbackResponse                                PaynetEasy callback
     */
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse);
}