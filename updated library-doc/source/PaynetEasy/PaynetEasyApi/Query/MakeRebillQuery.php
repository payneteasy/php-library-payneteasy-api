<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQuery;
use PaynetEasy\PaynetEasyApi\Util\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class MakeRebillQuery extends PaymentQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',         'payment.clientId',                             true,    Validator::ID),
        array('order_desc',             'payment.description',                          true,    Validator::LONG_STRING),
        array('amount',                 'payment.amount',                               true,    Validator::AMOUNT),
        array('currency',               'payment.currency',                             true,    Validator::CURRENCY),
        array('ipaddress',              'payment.customer.ipAddress',                   true,    Validator::IP),
        array('cardrefid',              'payment.recurrentCardFrom.paynetId',           true,    Validator::ID),
        array('login',                  'queryConfig.login',                            true,    Validator::MEDIUM_STRING),
        // optional
        array('comment',                'payment.comment',                              false,   Validator::MEDIUM_STRING),
        array('cvv2',                   'payment.recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        array('server_callback_url',    'queryConfig.callbackUrl',                      false,   Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'payment.clientId',
        'payment.recurrentCardFrom.paynetId',
        'payment.amountInCents',
        'payment.currency',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_CAPTURE;
}
