<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;

class FakeWorkflow extends AbstractWorkflow
{
    static public $response;

    public $initialApiMethod;

    public function __construct()
    {
    }

    public function processPayment(Payment $payment, array $callbackData = array())
    {
        return static::$response;
    }

    public function setNeededAction(Response $response)
    {
        return parent::setNeededAction($response);
    }
}
