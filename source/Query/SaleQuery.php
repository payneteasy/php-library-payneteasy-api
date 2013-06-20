<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

class SaleQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                true,   '#^[\S\s]{1,128}$#i'),
        array('order_desc',         'description',                  true,   '#^[\S\s]{1,125}$#i'),
        array('amount',             'amount',                       true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',           'currency',                     true,   '#^[A-Z]{1,3}$#i'),
        array('ipaddress',          'ipAddress',                    true,   Validator::IP),
        array('address1',           'customer.address',             true,   '#^[\S\s]{2,50}$#i'),
        array('city',               'customer.city',                true,   '#^[\S\s]{2,50}$#i'),
        array('zip_code',           'customer.zipCode',             true,   '#^[\S\s]{1,10}$#i'),
        array('country',            'customer.country',             true,   '#^[A-Z]{1,2}$#i'),
        array('phone',              'customer.phone',               true,   '#^[0-9\-\+\(\)\s]{6,15}$#i'),
        array('email',              'customer.email',               true,   Validator::EMAIL),
        array('card_printed_name',  'creditCard.cardPrintedName',   true,   '#^[\S\s]{1,128}$#i'),
        array('credit_card_number', 'creditCard.creditCardNumber',  true,   '#^[0-9]{1,20}$#i'),
        array('expire_month',       'creditCard.expireMonth',       true,   Validator::MONTH),
        array('expire_year',        'creditCard.expireYear',        true,   '#^[0-9]{1,2}$#i'),
        array('cvv2',               'creditCard.cvv2',              true,   '#^[0-9]{3,4}$#i'),
        // optional
        array('first_name',         'customer.firstName',           false,  '#^[^0-9]{2,50}$#i'),
        array('last_name',          'customer.lastName',            false,  '#^[^0-9]{2,50}$#i'),
        array('ssn',                'customer.ssn',                 false,  '#^[0-9]{1,4}$#i'),
        array('birthday',           'customer.birthday',            false,  '#^[0-9]{6}$#i'),
        array('state',              'customer.state',               false,  '#^[A-Z]{1,2}$#i'),
        array('cell_phone',         'customer.cellPhone',           false,  '#^[0-9\-\+\(\)\s]{6,15}$#i'),
        array('site_url',           'siteUrl',                      false,   Validator::URL),
        array('destination',        'destination',                  false,  '#^[\S\s]{1,128}$#i'),
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
        'clientOrderId',
        'amountInCents',
        'customer.email',
        'control'
    );
}