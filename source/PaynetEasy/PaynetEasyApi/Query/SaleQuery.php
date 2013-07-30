<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQuery;
use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions
 */
class SaleQuery extends PaymentQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',         'payment.clientPaymentId',              true,   Validator::ID),
        array('order_desc',             'payment.description',                  true,   Validator::LONG_STRING),
        array('amount',                 'payment.amount',                       true,   Validator::AMOUNT),
        array('currency',               'payment.currency',                     true,   Validator::CURRENCY),
        array('address1',               'payment.billingAddress.firstLine',     true,   Validator::MEDIUM_STRING),
        array('city',                   'payment.billingAddress.city',          true,   Validator::MEDIUM_STRING),
        array('zip_code',               'payment.billingAddress.zipCode',       true,   Validator::ZIP_CODE),
        array('country',                'payment.billingAddress.country',       true,   Validator::COUNTRY),
        array('phone',                  'payment.billingAddress.phone',         true,   Validator::PHONE),
        array('ipaddress',              'payment.customer.ipAddress',           true,   Validator::IP),
        array('email',                  'payment.customer.email',               true,   Validator::EMAIL),
        array('card_printed_name',      'payment.creditCard.cardPrintedName',   true,   Validator::LONG_STRING),
        array('credit_card_number',     'payment.creditCard.creditCardNumber',  true,   Validator::CREDIT_CARD_NUMBER),
        array('expire_month',           'payment.creditCard.expireMonth',       true,   Validator::MONTH),
        array('expire_year',            'payment.creditCard.expireYear',        true,   Validator::YEAR),
        array('cvv2',                   'payment.creditCard.cvv2',              true,   Validator::CVV2),
        array('redirect_url',           'queryConfig.redirectUrl',              true,   Validator::URL),
        // optional
        array('first_name',             'payment.customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',              'payment.customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                    'payment.customer.ssn',                 false,  Validator::SSN),
        array('birthday',               'payment.customer.birthday',            false,  Validator::DATE),
        array('state',                  'payment.billingAddress.state',         false,  Validator::COUNTRY),
        array('cell_phone',             'payment.billingAddress.cellPhone',     false,  Validator::PHONE),
        array('destination',            'payment.destination',                  false,  Validator::LONG_STRING),
        array('site_url',               'queryConfig.siteUrl',                  false,  Validator::URL),
        array('server_callback_url',    'queryConfig.callbackUrl',              false,  Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.endPoint',
        'payment.clientPaymentId',
        'payment.amountInCents',
        'payment.customer.email',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_CAPTURE;
}