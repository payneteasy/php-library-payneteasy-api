<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Query\QueryFactoryInterface;
use RuntimeException;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class FormWorkflow extends AbstractWorkflow
{
    /**
     * Allowed form query methods
     *
     * @var array
     */
    static protected $allowedInitialApiMethods = array
    (
        'sale-form',
        'preauth-form',
        'transfer-form'
    );

    /**
     * {@inheritdoc}
     */
    public function __construct(GatewayClientInterface  $gatewayClient,
                                QueryFactoryInterface   $queryFactory,
                                array                   $queryConfig  = array())
    {
        $this->gatewayClient = $gatewayClient;
        $this->queryFactory  = $queryFactory;
        $this->queryConfig   = $queryConfig;
    }

    /**
     * Indirectly sets initial API query method
     *
     * @param       string      $apiMethod      Initial API query method
     */
    public function setInitialApiMethod($apiMethod)
    {
        if (!in_array($apiMethod, static::$allowedInitialApiMethods))
        {
            throw new RuntimeException("Unknown initial api method: {$apiMethod}");
        }

        $this->initialApiMethod = $apiMethod;
    }
}