<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

interface WorkflowInterface
{
    /**
     * Process Payment with different transport stage
     *
     * @param       PaynetEasy\PaynetEasyApi\PaymentData\Payment       $payment     Payment for processing
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response                    Response object
     */
    public function processPayment(Payment $payment);
}