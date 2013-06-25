<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Order_status
 */
class StatusQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                    true,    Validator::ID),
        array('orderid',            'paynetOrderId',                    true,    Validator::ID),
        // generated
        array('control',             null,                              true,    null),
        // from config
        array('login',               null,                              true,    null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientOrderId',
        'paynetOrderId',
        'control'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'status-response';
}