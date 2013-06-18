<?php

namespace PaynetEasy\Paynet\Workflow;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class FormWorkflow extends SaleWorkflow
{
    /**
     * Indirectly sets initial API query method
     *
     * @param       string      $apiMethod      Initial API query method
     */
    public function setInitialApiMethod($apiMethod)
    {
        $this->initialApiMethod = $apiMethod;
    }
}