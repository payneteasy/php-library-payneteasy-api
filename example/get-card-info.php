<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала
 * получить id кредитной карты, выполнив запрос create-card-ref
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Создадим новый платеж.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getQueryConfig()
 */
$paymentTransaction = new PaymentTransaction(array
(
    'payment'               => new Payment(array
    (
        'recurrent_card_from'   =>  new RecurrentCard(array
        (
            'paynet_id'             => 8058
        ))
    )),
    'query_config'          =>  $getQueryConfig()
));

/**
 * Вызов этого метода заполнит поля объекта RecurrentCard, размещенного в объекте Payment
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::updatePaymentOnSuccess()
 */
$getPaymentProcessor()->executeQuery('get-card-info', $paymentTransaction);