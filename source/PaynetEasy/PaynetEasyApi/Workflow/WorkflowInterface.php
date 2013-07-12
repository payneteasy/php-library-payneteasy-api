<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;

interface WorkflowInterface
{
    /**
     * Process Order with different transport stage
     *
     * @param       PaynetEasy\PaynetEasyApi\OrderData\OrderInterface   $order              Order for processing
     * @param       array                                   $callbackData       Paynet callback data
     *
     * @return      \PaynetEasy\PaynetEasyApi\Transport\Response
     */
    public function processOrder(OrderInterface $order, array $callbackData = array());
}