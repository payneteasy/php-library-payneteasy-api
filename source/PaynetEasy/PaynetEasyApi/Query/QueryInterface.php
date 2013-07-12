<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\Response;

interface QueryInterface
{
    /**
     * Create API gateway Request from Order data
     *
     * @param       \PaynetEasy\PaynetEasyApi\OrderData\OrderInterface      $order          Order for query
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Request                        Request object
     */
    public function createRequest(OrderInterface $order);

    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\PaynetEasyApi\OrderData\OrderInterface      $order          Order for update
     * @param       \PaynetEasy\PaynetEasyApi\Transport\Response       $response       API gateway Response
     */
    public function processResponse(OrderInterface $order, Response $response);
}