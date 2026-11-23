<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * First stage of the payment processing.
 * Creating a new payment, running the request transfer-by-ref
 */
if (!isset($_GET['stage']))
{
    /**
     * Create a new payment
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'               => new Payment(array
        (
            'client_id'             => 'CLIENT-112244',
            'amount'                =>  9.99,
            'currency'              => 'USD',
            'customer'              =>  new Customer(array
            (
                'ip_address'            => '127.0.0.1'
            )),
            'recurrent_card_from'   =>  new RecurrentCard(array
            (
                'paynet_id'             => 8058,
                'cvv2'                  => 123
            )),
            'recurrent_card_to'     =>  new RecurrentCard(array
            (
                'paynet_id'             => 8059
            )),
        )),
        'query_config'          =>  $getQueryConfig()
    ));

    /**
     * Run the request transfer-by-ref
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('transfer-by-ref', $paymentTransaction);
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