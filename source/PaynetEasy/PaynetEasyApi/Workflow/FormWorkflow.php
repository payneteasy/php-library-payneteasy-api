<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

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
}