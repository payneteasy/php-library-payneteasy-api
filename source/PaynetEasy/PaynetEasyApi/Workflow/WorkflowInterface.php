<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;

interface WorkflowInterface
{
    /**
     * Process Payment with different transport stage
     *
     * @param       PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface       $payment            Payment for processing
     * @param       array                                                       $callbackData       Paynet callback data
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response
     */
    public function processPayment(PaymentInterface $payment, array $callbackData = array());
}