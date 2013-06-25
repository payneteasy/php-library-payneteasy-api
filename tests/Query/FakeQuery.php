<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;

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

    protected function createControlCode(OrderInterface $order)
    {
        return 'control';
    }

    protected function orderToRequest(OrderInterface $order)
    {
        return array('_');
    }

    protected function validateOrder(OrderInterface $order)
    {
    }

    protected function validateResponseOnSuccess(OrderInterface $order, Response $response)
    {
    }

    protected function validateResponseOnError(OrderInterface $order, Response $response)
    {
    }

    protected function updateOrderOnSuccess(OrderInterface $order, Response $response)
    {
    }

    protected function updateOrderOnError(OrderInterface $order, Response $response)
    {
    }
}