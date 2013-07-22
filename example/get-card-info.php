<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала
 * получить id кредитной карты, выполнив запрос create-card-ref
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Если платеж был сохранен - получим его сохраненную версию, иначе создадим новый.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 */
$payment = $loadPayment() ?: new Payment(array());

/**
 * Установим конфигурацию для выполнения запроса
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getConfig()
 */
$payment->setQueryConfig($getConfig());

/**
 * Для этого запроса необходимо передать ID кредитной карты
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
 */
$payment->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058)));

$paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');

/**
 * Вызов этого метода заполнит поля объекта RecurrentCard, размещенного в объекте Payment
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::updatePaymentOnSuccess()
 */
$paymentProcessor->executeQuery('get-card-info', $payment, $_REQUEST);

/**
 * Сохраним платеж и выведем его на экран
 */
$savePayment($payment);
$displayEndedPayment($payment);
