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

/**
 * Создадим обработчик платежей и назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::callHandler()
 */
$paymentProcessor = new PaymentProcessor(array
(
    PaymentProcessor::HANDLER_CATCH_EXCEPTION     => $displayException,
    PaymentProcessor::HANDLER_SAVE_PAYMENT        => $savePayment,
    PaymentProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedPayment
));

/**
 * Вызов этого метода заполнит поля объекта RecurrentCard, размещенного в объекте Payment
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\GetCardInfoQuery::updatePaymentOnSuccess()
 */
$paymentProcessor->executeQuery('get-card-info', $payment);