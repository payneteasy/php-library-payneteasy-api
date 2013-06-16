<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\OrderData\OrderInterface;

class FakeWorkflow extends AbstractWorkflow
{
    static public $response;

    public function __construct()
    {
    }

    public function processOrder(OrderInterface $order, array $callbackData = array())
    {
        return static::$response;
    }
}
