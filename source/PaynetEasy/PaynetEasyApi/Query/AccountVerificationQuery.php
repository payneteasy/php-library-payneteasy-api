<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\Query;
use PaynetEasy\PaynetEasyApi\Util\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * @see http://doc.payneteasy.com/doc/account-verification.htm
 */
class AccountVerificationQuery extends Query {

    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',         'payment.clientId',                     true,   Validator::ID),
        array('order_desc',             'payment.description',                  true,   Validator::LONG_STRING),
        array('card_printed_name',      'payment.creditCard.cardPrintedName',   true,   Validator::LONG_STRING),
        array('address1',               'payment.billingAddress.firstLine',     true,   Validator::MEDIUM_STRING),
        array('city',                   'payment.billingAddress.city',          true,   Validator::MEDIUM_STRING),
        array('zip_code',               'payment.billingAddress.zipCode',       true,   Validator::ZIP_CODE),
        array('country',                'payment.billingAddress.country',       true,   Validator::COUNTRY),
        array('email',                  'payment.customer.email',               true,   Validator::EMAIL),
        array('credit_card_number',     'payment.creditCard.creditCardNumber',  true,   Validator::CREDIT_CARD_NUMBER),
        array('expire_month',           'payment.creditCard.expireMonth',       true,   Validator::MONTH),
        array('expire_year',            'payment.creditCard.expireYear',        true,   Validator::YEAR),
        array('cvv2',                   'payment.creditCard.cvv2',              true,   Validator::CVV2),
        array('ipaddress',              'payment.customer.ipAddress',           true,   Validator::IP),
        // optional
        array('first_name',             'payment.customer.firstName',           false,  Validator::MEDIUM_STRING),
        array('last_name',              'payment.customer.lastName',            false,  Validator::MEDIUM_STRING),
        array('ssn',                    'payment.customer.ssn',                 false,  Validator::SSN),
        array('birthday',               'payment.customer.birthday',            false,  Validator::DATE),
        array('state',                  'payment.billingAddress.state',         false,  Validator::STATE),
        array('cell_phone',             'payment.billingAddress.cellPhone',     false,  Validator::PHONE),
        array('phone',                  'payment.billingAddress.phone',         false,  Validator::PHONE),
        array('site_url',               'queryConfig.siteUrl',                  false,  Validator::URL),
        array('purpose',                'payment.destination',                  false,  Validator::LONG_STRING),
        array('server_callback_url',    'queryConfig.callbackUrl',              false,  Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-response';

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.endPoint',
        'payment.clientId',
        'payment.customer.email',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::updatePaymentTransactionOnSuccess($paymentTransaction, $response);

        if ($response->isProcessing())
        {
            $response->setNeededAction(Response::NEEDED_STATUS_UPDATE);
        }
    }
}
