<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Utils\String;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;
use PaynetEasy\Paynet\Callback\CallbackFactoryInterface;

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
     * API callbacks factory
     *
     * @var PaynetEasy\Paynet\Callback\CallbackFactoryInterface
     */
    protected $callbackFactory;

    /**
     *
     * @param       \PaynetEasy\Paynet\Transport\GatewayClientInterface         $gatewayClient      API gateway client
     * @param       \PaynetEasy\Paynet\Query\QueryFactoryInterface              $queryFactory       API queries factory
     * @param       \PaynetEasy\Paynet\Callback\CallbackFactoryInterface        $callbackFactory    API callbacks factory
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
    public function getWorkflow($workflowName, array $workflowConfig)
    {
        $workflowClass  = __NAMESPACE__ . '\\' . String::camelize($workflowName) . 'Workflow';

        if (class_exists($workflowClass, true))
        {
            return new $workflowClass($this->gatewayClient,
                                      $this->queryFactory,
                                      $this->callbackFactory,
                                      $workflowConfig);
        }

        // :NOTICE:         Imenem          18.06.13
        //
        // All "*-form" methods has the same format,
        // therefore they have only one class - FormWorkflow
        if (preg_match('#.*-form$#i', $workflowName))
        {
            $workflow = new FormWorkflow($this->gatewayClient,
                                         $this->queryFactory,
                                         $this->callbackFactory,
                                         $workflowConfig);

            $workflow->setInitialApiMethod($workflowName);

            return $workflow;
        }

        throw new RuntimeException("Unknown workflow class {$workflowClass} for workflow with name {$workflowName}");
    }
}