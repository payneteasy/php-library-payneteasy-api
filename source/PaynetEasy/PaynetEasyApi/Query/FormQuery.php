<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;
use RuntimeException;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration
 */
class FormQuery extends AbstractQuery
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
        // optional
        array('site_url',           'siteUrl',                      false,  Validator::URL),
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
        'serial-number',
        'redirect-url'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-form-response';

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
            throw new RuntimeException("Unknown api method: '{$apiMethod}'");
        }

        $this->apiMethod = $apiMethod;
    }
}