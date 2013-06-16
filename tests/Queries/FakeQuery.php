<?php

namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;

class FakeQuery extends AbstractQuery
{
    static public $request;

    public function __construct()
    {
    }

    public function createRequest(OrderInterface $order)
    {
        return static::$request;
    }

    public function processResponse(OrderInterface $order, Response $response)
    {
        return $response;
    }
}