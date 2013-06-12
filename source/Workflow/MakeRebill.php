<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;

/**
 * The implementation of the query MakeRebill
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class MakeRebill extends Sale
{
    protected function initQuery(OrderInterface $order)
    {
        return $this->executeQuery('make-rebill', $order);
    }
}