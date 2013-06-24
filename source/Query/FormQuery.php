<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;
use RuntimeException;

class FormQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                true,   Validator::ID),
        array('order_desc',         'description',                  true,   Validator::ID),
        array('amount',             'amount',                       true,   Validator::AMOUNT),
        array('currency',           'currency',                     true,   Validator::CURRENCY),
        array('ipaddress',          'ipAddress',                    true,   Validator::IP),
        array('site_url',           'siteUrl',                      false,  Validator::URL),
        array('address1',           'customer.address',             true,   Validator::MEDIUM_STRING),
        array('city',               'customer.city',                true,   Validator::MEDIUM_STRING),
        array('zip_code',           'customer.zipCode',             true,   Validator::ZIP_CODE),
        array('country',            'customer.country',             true,   Validator::COUNTRY),
        array('phone',              'customer.phone',               true,   Validator::PHONE),
        array('email',              'customer.email',               true,   Validator::EMAIL),
        // optional
        array('first_name',         'customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',          'customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                'customer.ssn',                 false,  Validator::SSN),
        array('birthday',           'customer.birthday',            false,  Validator::DATE),
        array('state',              'customer.state',               false,  Validator::COUNTRY),
        array('cell_phone',         'customer.cellPhone',           false,  Validator::PHONE),
        // generated
        array('control',             null,                          true,    null),
        // from config
        array('redirect_url',        null,                          true,    null),
        array('server_callback_url', null,                          false,   null)
    );

    /**
     * Allowed form query methods
     *
     * @var array
     */
    static protected $allowedApiMethods = array
    (
        'sale-form',
        'preauth-form',
        'transfer-form'
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

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }

    /**
     * Indirectly sets gateway API query method
     *
     * @param       string      $apiMethod      Gateway API method
     */
    public function setApiMethod($apiMethod)
    {
        if (!in_array($apiMethod, static::$allowedApiMethods))
        {
            throw new RuntimeException("Unknown api method: {$apiMethod}");
        }

        $this->apiMethod = $apiMethod;
    }
}