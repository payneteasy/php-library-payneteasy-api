<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;

class FakeQuery implements QueryInterface
{
    static public $request;

    public function createRequest(Payment $payment)
    {
        return static::$request;
    }

    public function processResponse(Payment $payment, Response $response)
    {
        if ($response->isApproved())
        {
            $payment->setProcessingStage(Payment::STAGE_FINISHED);
            $payment->setStatus(Payment::STATUS_APPROVED);
        }

        return $response;
    }
}