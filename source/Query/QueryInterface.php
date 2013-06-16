<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;

interface QueryInterface
{
    /**
     * Create API gateway Request from Order data
     *
     * @param       \PaynetEasy\Paynet\Data\OrderInterface      $order          Order for query
     *
     * @return      \PaynetEasy\Paynet\Transport\Request                        Request object
     */
    public function createRequest(OrderInterface $order);

    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\Paynet\Data\OrderInterface      $order          Order for update
     * @param       \PaynetEasy\Paynet\Transport\Response       $response       API gateway Response
     */
    public function processResponse(OrderInterface $order, Response $response);
}