<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала провести
 * платеж одним из следующих способов: sale, preauth, sale-form, preauth-form
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Создадим новый платеж.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_registration_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see common/functions.php, $getQueryConfig()
 */
$paymentTransaction = new PaymentTransaction(array
(
    'payment'               => new Payment(array
    (
        'client_id'             => 'CLIENT-112244',
        'paynet_id'             =>  1969595,
        'status'                =>  Payment::STATUS_PREAUTH
    )),
    'status'                =>  PaymentTransaction::STATUS_APPROVED,
    'query_config'          =>  $getQueryConfig()
));

/**
 * Вызов этого метода создаст в объекте Payment объект RecurrentCard
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::updatePaymentOnSuccess()
 */
$getPaymentProcessor()->executeQuery('create-card-ref', $paymentTransaction);