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
        array('client_orderid',     'payment.clientPaymentId',          true,   Validator::ID),
        array('orderid',            'payment.paynetPaymentId',          true,   Validator::ID),
        array('amount',             'payment.amount',                   true,   Validator::AMOUNT),
        array('currency',           'payment.currency',                 true,   Validator::CURRENCY),
        array('comment',            'payment.comment',                  true,   Validator::MEDIUM_STRING),
        array('login',              'queryConfig.login',                true,   Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'payment.clientPaymentId',
        'payment.paynetPaymentId',
        'payment.amountInCents',
        'payment.currency',
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