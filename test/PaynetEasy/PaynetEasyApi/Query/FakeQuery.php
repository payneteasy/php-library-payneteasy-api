<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;
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

    protected function createControlCode(PaymentInterface $payment)
    {
        return 'control';
    }

    protected function paymentToRequest(PaymentInterface $payment)
    {
        return array('_');
    }

    protected function validatePayment(PaymentInterface $payment)
    {
    }

    protected function validateResponseOnSuccess(PaymentInterface $payment, Response $response)
    {
    }

    protected function validateResponseOnError(PaymentInterface $payment, Response $response)
    {
    }

    protected function updatePaymentOnSuccess(PaymentInterface $payment, Response $response)
    {
    }

    protected function updatePaymentOnError(PaymentInterface $payment, Response $response)
    {
    }
}