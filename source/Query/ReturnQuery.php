<?php
namespace PaynetEasy\Paynet\Query;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                    true,   '#^[\S\s]{1,128}$#i'),
        array('orderid',            'paynetOrderId',                    true,   '#^[\S\s]{1,20}$#i'),
        array('amount',             'amount',                           true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',           'currency',                         true,   '#^[A-Z]{1,3}$#i'),
        array('comment',            'comment',                          true,  '#^[\S\s]{1,50}$#i'),
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
        'amountInCents',
        'currency',
        'control'
    );
}