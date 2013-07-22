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
        array('client_orderid',         'clientPaymentId',                      true,    Validator::ID),
        array('order_desc',             'description',                          true,    Validator::LONG_STRING),
        array('amount',                 'amount',                               true,    Validator::AMOUNT),
        array('currency',               'currency',                             true,    Validator::CURRENCY),
        array('ipaddress',              'customer.ipAddress',                   true,    Validator::IP),
        array('cardrefid',              'recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        array('login',                  'queryConfig.login',                    true,    Validator::MEDIUM_STRING),
        // optional
        array('comment',                'comment',                              false,   Validator::MEDIUM_STRING),
        array('cvv2',                   'recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        array('server_callback_url',    'queryConfig.callbackUrl',              false,   Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.endPoint',
        'clientPaymentId',
        'amountInCents',
        'recurrentCardFrom.cardReferenceId',
        'queryConfig.signingKey'
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