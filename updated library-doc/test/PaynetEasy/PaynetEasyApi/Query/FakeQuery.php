<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;

class FakeQuery implements QueryInterface
{
    static public $request;

    public function createRequest(PaymentTransaction $paymentTransaction)
    {
        return static::$request;
    }

    public function processResponse(PaymentTransaction $paymentTransaction, Response $response)
    {
        if ($response->isApproved())
        {
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_APPROVED);
        }

        return $response;
    }
}