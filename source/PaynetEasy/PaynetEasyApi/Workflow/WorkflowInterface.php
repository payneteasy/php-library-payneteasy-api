<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

interface WorkflowInterface
{
    /**
     * Process Payment with different transport stage
     *
     * @param       PaynetEasy\PaynetEasyApi\PaymentData\Payment       $payment            Payment for processing
     * @param       array                                                       $callbackData       Paynet callback data
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response
     */
    public function processPayment(Payment $payment, array $callbackData = array());
}