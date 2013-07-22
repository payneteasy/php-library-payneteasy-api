<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

interface WorkflowFactoryInterface
{
    /**
     * Get workflow by their name.
     * Usually it is name of first workflow API method query.
     *
     * @param       string      $workflowName           Workflow name
     *
     * @return      WorkflowInterface                   Workflow for payment processing
     */
    public function getWorkflow($workflowName);
}