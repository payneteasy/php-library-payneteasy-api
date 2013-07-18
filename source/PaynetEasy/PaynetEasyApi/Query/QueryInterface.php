<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;

interface QueryInterface
{
    /**
     * Create API gateway Request from Payment data
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment        Payment for query
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Request                                 Request object
     */
    public function createRequest(Payment $payment);

    /**
     * Process API gateway Response and update Payment
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\Payment      $payment        Payment for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response                $response       API gateway Response
     */
    public function processResponse(Payment $payment, Response $response);
}