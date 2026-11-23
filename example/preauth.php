<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * First stage of the payment processing.
 * Creating a new payment, running the request preauth
 */
if (!isset($_GET['stage']))
{
    /**
     * Create a new payment
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\CreditCard
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'               => new Payment(array
        (
            'client_id'             => 'CLIENT-112244',
            'description'           => 'This is test payment',
            'amount'                =>  0.99,
            'currency'              => 'USD',
            'customer'              => new Customer(array
            (
                'email'                 => 'vass.pupkin@example.com',
                'ip_address'            => '127.0.0.1'
            )),
            'billing_address'       => new BillingAddress(array
            (
                'country'               => 'US',
                'state'                 => 'TX',
                'city'                  => 'Houston',
                'first_line'            => '2704 Colonial Drive',
                'zip_code'              => '1235',
                'phone'                 => '660-485-6353'
            )),
            'credit_card'           => new CreditCard(array
            (
                'card_printed_name'     => 'Vasya Pupkin',
                'credit_card_number'    => '4444 5555 6666 1111',
                'expire_month'          => '12',
                'expire_year'           => '14',
                'cvv2'                  => '123'
            ))
        )),
        'query_config'          =>  $getQueryConfig()
    ));

    /**
     * Run the request preauth
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('preauth', $paymentTransaction);
}
/**
 * Second stage of the payment processing.
 * Waiting for the payment status to change.
 */
elseif ($_GET['stage'] == 'updateStatus')
{
    /**
     * Request the payment status
     */
    $getPaymentProcessor()->executeQuery('status', $loadPaymentTransaction());
}
/**
 * Third stage of the payment processing.
 * Processing of the customer return from PaynetEasy after 3-D Secure authentication
 */
elseif ($_GET['stage'] == 'processCustomerReturn')
{
    /**
     * Process the data received from PaynetEasy
     */
    $getPaymentProcessor()->processCustomerReturn(new CallbackResponse($_POST), $loadPaymentTransaction());
}
/**
 * Additional stage of the payment processing.
 * Processing of the PaynetEasy callback.
 */
elseif ($_GET['stage'] == 'processPaynetEasyCallback')
{
    /**
     * Process the data received from PaynetEasy
     */
    $getPaymentProcessor()->processPaynetEasyCallback(new CallbackResponse($_GET), $loadPaymentTransaction());
}