<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\Transport\GatewayClientInterface;
use PaynetEasy\PaynetEasyApi\Query\QueryFactoryInterface;
use PaynetEasy\PaynetEasyApi\Callback\CallbackFactoryInterface;

use RuntimeException;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration
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
    public function __construct(GatewayClientInterface      $gatewayClient,
                                QueryFactoryInterface       $queryFactory,
                                CallbackFactoryInterface    $callbackFactory)
    {
        $this->gatewayClient    = $gatewayClient;
        $this->queryFactory     = $queryFactory;
        $this->callbackFactory  = $callbackFactory;
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
            throw new RuntimeException("Unknown initial api method: '{$apiMethod}'");
        }

        $this->initialApiMethod = $apiMethod;
    }
}