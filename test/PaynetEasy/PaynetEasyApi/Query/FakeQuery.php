<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;

class FakeQuery extends AbstractQuery
{
    static public $request;

    public $apiMethod;

    public function __construct()
    {
    }

    public function setApiMethod($class)
    {
        parent::setApiMethod($class);
    }

    protected function createSignature(Payment $payment)
    {
        return 'control';
    }

    protected function paymentToRequest(Payment $payment)
    {
        return array('_');
    }

    protected function validatePayment(Payment $payment)
    {
    }

    protected function validateResponseOnSuccess(Payment $payment, Response $response)
    {
    }

    protected function validateResponseOnError(Payment $payment, Response $response)
    {
    }

    protected function updatePaymentOnSuccess(Payment $payment, Response $response)
    {
    }

    protected function updatePaymentOnError(Payment $payment, Response $response)
    {
    }
}