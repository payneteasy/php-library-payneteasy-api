<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;

use RuntimeException;

class WorkflowFactory implements WorkflowFactoryInterface
{
    /**
     * API gateway client
     *
     * @var \PaynetEasy\Paynet\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API queries factory
     *
     * @var \PaynetEasy\Paynet\Query\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     *
     * @param       \PaynetEasy\Paynet\Transport\GatewayClientInterface         $gatewayClient      API gateway client
     * @param       \PaynetEasy\Paynet\Query\QueryFactoryInterface            $queryFactory       API queries factory
     */
    public function __construct(GatewayClientInterface  $gatewayClient,
                                QueryFactoryInterface   $queryFactory)
    {
        $this->gatewayClient = $gatewayClient;
        $this->queryFactory  = $queryFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function getWorkflow($workflowName, array $workflowConfig)
    {
        $nameChunks     = array_map('ucfirst', explode('-', $workflowName));
        $workflowClass  = __NAMESPACE__ . '\\' . implode('', $nameChunks) . 'Workflow';

        if (class_exists($workflowClass, true))
        {
            return new $workflowClass($this->gatewayClient, $this->queryFactory, $workflowConfig);
        }

        // :NOTICE:         Imenem          18.06.13
        //
        // All "*-form" methods has the same format,
        // therefore they have only one class - FormWorkflow
        if (end($nameChunks) == 'Form')
        {
            $workflow = new FormWorkflow($this->gatewayClient, $this->queryFactory, $workflowConfig);
            $workflow->setInitialApiMethod($workflowName);

            return $workflow;
        }

        throw new RuntimeException("Unknown workflow class {$workflowClass} for workflow with name {$workflowName}");
    }
}