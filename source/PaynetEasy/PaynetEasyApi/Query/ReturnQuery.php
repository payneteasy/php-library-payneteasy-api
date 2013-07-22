<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientPaymentId',                  true,   Validator::ID),
        array('orderid',            'paynetPaymentId',                  true,   Validator::ID),
        array('amount',             'amount',                           true,   Validator::AMOUNT),
        array('currency',           'currency',                         true,   Validator::CURRENCY),
        array('comment',            'comment',                          true,   Validator::MEDIUM_STRING),
        array('login',              'queryConfig.login',                true,   Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'clientPaymentId',
        'paynetPaymentId',
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