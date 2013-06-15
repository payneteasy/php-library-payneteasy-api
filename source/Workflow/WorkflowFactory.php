<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Queries\QueryFactoryInterface;

class WorkflowFactory implements WorkflowFactoryInterface
{
    /**
     * Paynet gateway client
     *
     * @var \PaynetEasy\Paynet\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API request queries factory
     *
     * @var \PaynetEasy\Paynet\Queries\QueryFactoryInterface
     */
    protected $queryFactory;

    public function __construct(GatewayClientInterface  $gatewayClient,
                                QueryFactoryInterface   $queryFactory)
    {
        $this->gatewayClient = $gatewayClient;
        $this->queryFactory  = $queryFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function getWorkflow($workflowName, array $workflowConfig = array())
    {
        $nameChunks     = array_map('ucfirst', explode('-', $workflowName));
        $workflowClass  = __NAMESPACE__ . '\\' . implode('', $nameChunks) . 'Workflow';

        return new $workflowClass($this->gatewayClient, $this->queryFactory, $workflowConfig);
    }
}