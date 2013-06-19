<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;

interface CallbackInterface
{
    /**
     * Process API gateway Response and update Order
     *
     * @param       \PaynetEasy\Paynet\OrderData\OrderInterface         $order          Order for update
     * @param       \PaynetEasy\Paynet\Transport\Response               $response       API gateway Response
     */
    public function processResponse(OrderInterface $order, Response $response);
}