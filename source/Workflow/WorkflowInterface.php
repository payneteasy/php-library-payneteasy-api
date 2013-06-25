<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\OrderData\OrderInterface;

interface WorkflowInterface
{
    /**
     * Process Order with different transport stage
     *
     * @param       PaynetEasy\Paynet\OrderData\OrderInterface   $order              Order for processing
     * @param       array                                   $callbackData       Paynet callback data
     *
     * @return      \PaynetEasy\Paynet\Transport\Response
     */
    public function processOrder(OrderInterface $order, array $callbackData = array());
}