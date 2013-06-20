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
        array('client_orderid',     'clientOrderId',                true,   '#^[\S\s]{1,128}$#i'),
        array('order_desc',         'description',                  true,   '#^[\S\s]{1,125}$#i'),
        array('amount',             'amount',                       true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',           'currency',                     true,   '#^[A-Z]{1,3}$#i'),
        array('ipaddress',          'ipAddress',                    true,   Validator::IP),
        array('site_url',           'siteUrl',                      false,   Validator::URL),
        array('address1',           'customer.address',             true,   '#^[\S\s]{2,50}$#i'),
        array('city',               'customer.city',                true,   '#^[\S\s]{2,50}$#i'),
        array('zip_code',           'customer.zipCode',             true,   '#^[\S\s]{1,10}$#i'),
        array('country',            'customer.country',             true,   '#^[A-Z]{1,2}$#i'),
        array('phone',              'customer.phone',               true,   '#^[0-9\-\+\(\)\s]{6,15}$#i'),
        array('email',              'customer.email',               true,   Validator::EMAIL),
        // optional
        array('first_name',         'customer.firstName',           false,  '#^[^0-9]{2,50}$#i'),
        array('last_name',          'customer.lastName',            false,  '#^[^0-9]{2,50}$#i'),
        array('ssn',                'customer.ssn',                 false,  '#^[0-9]{1,4}$#i'),
        array('birthday',           'customer.birthday',            false,  '#^[0-9]{6}$#i'),
        array('state',              'customer.state',               false,  '#^[A-Z]{1,2}$#i'),
        array('cell_phone',         'customer.cellPhone',           false,  '#^[0-9\-\+\(\)\s]{6,15}$#i'),
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