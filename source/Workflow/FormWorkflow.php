<?php

namespace PaynetEasy\Paynet\Workflow;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class FormWorkflow extends SaleWorkflow
{
    protected $initialApiMethod = 'sale-form';
}