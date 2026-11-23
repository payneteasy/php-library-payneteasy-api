<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

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
     * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions#Return_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'       => new Payment(array
        (
            'client_id'     => 'CLIENT-112244',
            'paynet_id'     =>  1969589,
            'amount'        =>  9.99,
            'currency'      => 'USD',
            'comment'       => 'cancel payment',
            'status'        =>  Payment::STATUS_CAPTURE
        )),
        'query_config'  =>  $getQueryConfig()
    ));

    /**
     * Run the request return
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('return', $paymentTransaction);
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
