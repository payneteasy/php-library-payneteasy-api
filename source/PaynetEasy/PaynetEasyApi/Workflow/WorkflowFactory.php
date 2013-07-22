<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\Utils\String;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use RuntimeException;

class WorkflowFactory implements WorkflowFactoryInterface
{
    /**
     * API gateway client
     *
     * @var \PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface
     */
    protected $gatewayClient;

    /**
     * API queries factory
     *
     * @var \PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * API callbacks factory
     *
     * @var PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface
     */
    protected $callbackFactory;

    /**
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface         $gatewayClient      API gateway client
     * @param       \PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface              $queryFactory       API queries factory
     * @param       \PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface        $callbackFactory    API callbacks factory
     */
    public function __construct(GatewayClientInterface      $gatewayClient,
                                QueryFactoryInterface       $queryFactory,
                                CallbackFactoryInterface    $callbackFactory)
    {
        $this->gatewayClient    = $gatewayClient;
        $this->queryFactory     = $queryFactory;
        $this->callbackFactory  = $callbackFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function getWorkflow($workflowName)
    {
        $workflowClass  = __NAMESPACE__ . '\\' . String::camelize($workflowName) . 'Workflow';

        if (class_exists($workflowClass, true))
        {
            return new $workflowClass($workflowName,
                                      $this->gatewayClient,
                                      $this->queryFactory,
                                      $this->callbackFactory);
        }

        // :NOTICE:         Imenem          18.06.13
        //
        // All "*-form" methods has the same format,
        // therefore they have only one class - FormWorkflow
        if (preg_match('#.*-form$#i', $workflowName))
        {
            return new FormWorkflow($workflowName,
                                    $this->gatewayClient,
                                    $this->queryFactory,
                                    $this->callbackFactory);
        }

        throw new RuntimeException("Unknown workflow class '{$workflowClass}' for workflow with name '{$workflowName}'");
    }
}