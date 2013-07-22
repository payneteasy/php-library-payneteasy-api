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
        array('ipaddress',                  'customer.ipAddress',                   true,    Validator::IP),
        array('destination-card-ref-id',    'recurrentCardTo.cardReferenceId',      true,    Validator::ID),
        array('login',                      'queryConfig.login',                    true,    Validator::MEDIUM_STRING),
        // optional
        array('order_desc',                 'description',                          false,   Validator::LONG_STRING),
        array('source-card-ref-id',         'recurrentCardFrom.cardReferenceId',    false,   Validator::ID),
        array('cvv2',                       'recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        array('redirect_url',               'queryConfig.redirectUrl',              false,   Validator::URL),
        array('server_callback_url',        'queryConfig.callbackUrl',              false,   Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'clientPaymentId',
        'recurrentCardFrom.cardReferenceId',
        'recurrentCardTo.cardReferenceId',
        'amountInCents',
        'currency',
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