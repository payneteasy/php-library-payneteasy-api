<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use Exception;

class ExceptionQuery extends FakeQuery
{
    public function processResponse(Payment $payment, Response $response)
    {
        throw new Exception('Process response exception');
    }
}