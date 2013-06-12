<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class SaleForm extends Sale
{
    protected function initQuery(OrderInterface $order)
    {
        return $this->executeQuery('sale-form', $order);
    }
}