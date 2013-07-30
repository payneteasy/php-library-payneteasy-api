<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration
 */
abstract class AbstractFormQuery extends AbstractPaymentQuery
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
        array('redirect_url',           'queryConfig.redirectUrl',              true,   Validator::URL),
        // optional
        array('first_name',             'payment.customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',              'payment.customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                    'payment.customer.ssn',                 false,  Validator::SSN),
        array('birthday',               'payment.customer.birthday',            false,  Validator::DATE),
        array('state',                  'payment.billingAddress.state',         false,  Validator::COUNTRY),
        array('cell_phone',             'payment.billingAddress.cellPhone',     false,  Validator::PHONE),
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
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::updatePaymentTransactionOnSuccess($paymentTransaction, $response);

        if ($response->hasRedirectUrl())
        {
            $response->setNeededAction(Response::NEEDED_REDIRECT);
        }
    }
}