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
 * Creating a new payment, running the request make-rebill.
 */
if (!isset($_GET['stage']))
{
    /**
     * Note that before this request you have to
     * get the credit card id with the create-card-ref request
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
     *
     * Create a new payment.
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
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
            'customer'              =>  new Customer(array
            (
                'ip_address'            => '127.0.0.1'
            )),
            'recurrent_card_from'   => new RecurrentCard(array
            (
                'paynet_id'             => 8058
            ))
        )),
        'query_config'          =>  $getQueryConfig(),
    ));

    /**
     * Run the request make-rebill
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('make-rebill', $paymentTransaction);
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