<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use Exception;

class ExceptionQuery extends FakeQuery
{
    public function processResponse(PaymentTransaction $paymentTransaction, Response $response)
    {
        throw new Exception('Process response exception');
    }
}