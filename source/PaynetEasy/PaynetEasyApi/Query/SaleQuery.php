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
        array('client_orderid',     'clientPaymentId',              true,   Validator::ID),
        array('order_desc',         'description',                  true,   Validator::LONG_STRING),
        array('amount',             'amount',                       true,   Validator::AMOUNT),
        array('currency',           'currency',                     true,   Validator::CURRENCY),
        array('ipaddress',          'ipAddress',                    true,   Validator::IP),
        array('address1',           'customer.address',             true,   Validator::MEDIUM_STRING),
        array('city',               'customer.city',                true,   Validator::MEDIUM_STRING),
        array('zip_code',           'customer.zipCode',             true,   Validator::ZIP_CODE),
        array('country',            'customer.country',             true,   Validator::COUNTRY),
        array('phone',              'customer.phone',               true,   Validator::PHONE),
        array('email',              'customer.email',               true,   Validator::EMAIL),
        array('card_printed_name',  'creditCard.cardPrintedName',   true,   Validator::LONG_STRING),
        array('credit_card_number', 'creditCard.creditCardNumber',  true,   Validator::CREDIT_CARD_NUMBER),
        array('expire_month',       'creditCard.expireMonth',       true,   Validator::MONTH),
        array('expire_year',        'creditCard.expireYear',        true,   Validator::YEAR),
        array('cvv2',               'creditCard.cvv2',              true,   Validator::CVV2),
        // optional
        array('first_name',         'customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',          'customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                'customer.ssn',                 false,  Validator::SSN),
        array('birthday',           'customer.birthday',            false,  Validator::DATE),
        array('state',              'customer.state',               false,  Validator::COUNTRY),
        array('cell_phone',         'customer.cellPhone',           false,  Validator::PHONE),
        array('site_url',           'siteUrl',                      false,  Validator::URL),
        array('destination',        'destination',                  false,  Validator::LONG_STRING),
        // generated
        array('control',             null,                          true,    null),
        // from config
        array('redirect_url',        null,                          true,    null),
        array('server_callback_url', null,                          false,   null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'end_point',
        'clientPaymentId',
        'amountInCents',
        'customer.email',
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