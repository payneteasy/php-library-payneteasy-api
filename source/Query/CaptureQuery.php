<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

/**
 * The implementation of the query Capture
 * http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Process_Capture_Transaction
 */
class CaptureQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                true,   Validator::ID),
        array('orderid',            'paynetOrderId',                true,   Validator::ID),
        array('amount',             'amount',                       true,   Validator::AMOUNT),
        array('currency',           'currency',                     true,   Validator::CURRENCY),
        // generated
        array('control',             null,                          true,    null),
        // from config
        array('login',               null,                          true,    null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientOrderId',
        'paynetOrderId',
        'amountInCents',
        'currency',
        'control'
    );
}