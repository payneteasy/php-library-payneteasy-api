<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQuery;
use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Process_Capture_Transaction
 */
class CaptureQuery extends PaymentQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'payment.clientPaymentId',      true,   Validator::ID),
        array('orderid',            'payment.paynetPaymentId',      true,   Validator::ID),
        array('login',              'queryConfig.login',            true,   Validator::MEDIUM_STRING)
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
    static protected $paymentStatus = Payment::STATUS_CAPTURE;
}