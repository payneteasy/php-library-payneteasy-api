<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientPaymentId',                      true,    Validator::ID),
        array('order_desc',         'description',                          true,    Validator::LONG_STRING),
        array('amount',             'amount',                               true,    Validator::AMOUNT),
        array('currency',           'currency',                             true,    Validator::CURRENCY),
        array('ipaddress',          'ipAddress',                            true,    Validator::IP),
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        // optional
        array('comment',            'comment',                              false,   Validator::MEDIUM_STRING),
        array('cvv2',               'recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        // generated
        array('control',             null,                                  true,    null),
        // from config
        array('login',               null,                                  true,    null),
        array('server_callback_url', null,                                  false,   null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'end_point',
        'clientPaymentId',
        'amountInCents',
        'recurrentCardFrom.cardReferenceId',
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
    static protected $successResponseType = 'async-response';
}