<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;

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

        return new $workflowClass($this->gatewayClient, $this->queryFactory, $workflowConfig);
    }
}