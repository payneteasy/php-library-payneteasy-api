<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions
 */
class TransferByRefQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',             'clientPaymentId',                      true,    Validator::ID),
        array('amount',                     'amount',                               true,    Validator::AMOUNT),
        array('currency',                   'currency',                             true,    Validator::CURRENCY),
        array('ipaddress',                  'ipAddress',                            true,    Validator::IP),
        array('destination-card-ref-id',    'recurrentCardTo.cardReferenceId',      true,    Validator::ID),
        // optional
        array('order_desc',                 'description',                          false,   Validator::LONG_STRING),
        array('source-card-ref-id',         'recurrentCardFrom.cardReferenceId',    false,   Validator::ID),
        array('cvv2',                       'recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        // generated
        array('control',                    null,                                   true,    null),
        // from config
        array('login',                      null,                                   true,    null),
        array('redirect_url',               null,                                   false,   null),
        array('server_callback_url',        null,                                   false,   null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientPaymentId',
        'recurrentCardFrom.cardReferenceId',
        'recurrentCardTo.cardReferenceId',
        'amountInCents',
        'currency',
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