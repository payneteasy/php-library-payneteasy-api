<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Note that before this request you have to
 * run any request with asynchronous processing:
 * sale, preauth, capture, transfer-by-ref, make-rebill, return
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 *
 * If the payment was saved, load the saved one, otherwise create a new one.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Payment_status_call_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\StatusQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see common/functions.php, $getQueryConfig()
 */
$paymentTransaction = $loadPaymentTransaction() ?: new PaymentTransaction(array
(
    'payment'       => new Payment(array
    (
        'client_id'     => 'CLIENT-112244',
        'paynet_id'     =>  1969595
    )),
    'query_config'  =>  $getQueryConfig()
));

/**
 * This call updates the payment processing status
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\Status::updatePaymentOnSuccess()
 */
$getPaymentProcessor()->executeQuery('status', $paymentTransaction);