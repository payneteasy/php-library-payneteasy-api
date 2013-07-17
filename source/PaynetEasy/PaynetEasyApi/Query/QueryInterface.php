<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;
use PaynetEasy\PaynetEasyApi\Transport\Response;

interface QueryInterface
{
    /**
     * Create API gateway Request from Payment data
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment        Payment for query
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Request                                 Request object
     */
    public function createRequest(PaymentInterface $payment);

    /**
     * Process API gateway Response and update Payment
     *
     * @param       \PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface      $payment        Payment for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response                $response       API gateway Response
     */
    public function processResponse(PaymentInterface $payment, Response $response);
}