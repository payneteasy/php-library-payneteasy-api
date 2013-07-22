<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions
 */
class SaleQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',         'clientPaymentId',              true,   Validator::ID),
        array('order_desc',             'description',                  true,   Validator::LONG_STRING),
        array('amount',                 'amount',                       true,   Validator::AMOUNT),
        array('currency',               'currency',                     true,   Validator::CURRENCY),
        array('address1',               'billingAddress.firstLine',     true,   Validator::MEDIUM_STRING),
        array('city',                   'billingAddress.city',          true,   Validator::MEDIUM_STRING),
        array('zip_code',               'billingAddress.zipCode',       true,   Validator::ZIP_CODE),
        array('country',                'billingAddress.country',       true,   Validator::COUNTRY),
        array('phone',                  'billingAddress.phone',         true,   Validator::PHONE),
        array('ipaddress',              'customer.ipAddress',           true,   Validator::IP),
        array('email',                  'customer.email',               true,   Validator::EMAIL),
        array('card_printed_name',      'creditCard.cardPrintedName',   true,   Validator::LONG_STRING),
        array('credit_card_number',     'creditCard.creditCardNumber',  true,   Validator::CREDIT_CARD_NUMBER),
        array('expire_month',           'creditCard.expireMonth',       true,   Validator::MONTH),
        array('expire_year',            'creditCard.expireYear',        true,   Validator::YEAR),
        array('cvv2',                   'creditCard.cvv2',              true,   Validator::CVV2),
        array('redirect_url',           'queryConfig.redirectUrl',      true,   Validator::URL),
        // optional
        array('first_name',             'customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',              'customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                    'customer.ssn',                 false,  Validator::SSN),
        array('birthday',               'customer.birthday',            false,  Validator::DATE),
        array('state',                  'billingAddress.state',         false,  Validator::COUNTRY),
        array('cell_phone',             'billingAddress.cellPhone',     false,  Validator::PHONE),
        array('destination',            'destination',                  false,  Validator::LONG_STRING),
        array('site_url',               'queryConfig.siteUrl',          false,  Validator::URL),
        array('server_callback_url',    'queryConfig.callbackUrl',      false,  Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.endPoint',
        'clientPaymentId',
        'amountInCents',
        'customer.email',
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