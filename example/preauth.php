<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если платеж был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 */
$payment = $loadPayment() ?: new Payment(array
(
    'client_payment_id'     => 'CLIENT-112244',
    'description'           => 'This is test payment',
    'amount'                =>  0.99,
    'currency'              => 'USD'
));

/**
 * Установим конфигурацию для выполнения запроса
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getConfig()
 */
$payment->setQueryConfig($getConfig());

/**
 * Для этого запроса необходимо передать данные клиента
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
 */
$payment->setCustomer(new Customer(array
(
    'email'                 => 'vass.pupkin@example.com',
    'ip_address'            => '127.0.0.1'
)));

/**
 * Для этого запроса необходимо передать данные адреса
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
 */
$payment->setBillingAddress(new BillingAddress(array
(
    'country'               => 'US',
    'city'                  => 'Houston',
    'first_line'            => '2704 Colonial Drive',
    'zip_code'              => '1235',
    'phone'                 => '660-485-6353'
)));

/**
 * Для этого запроса необходимо передать данные кредитной карты
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\CreditCard
 */
$payment->setCreditCard(new CreditCard(array
(
    'card_printed_name'     => 'Vasya Pupkin',
    'credit_card_number'    => '4444 5555 6666 1111',
    'expire_month'          => '12',
    'expire_year'           => '14',
    'cvv2'                  => '123'
)));

/**
 * Создадим обработчик платежей
 */
$paymentProcessor = new PaymentProcessor;

/**
 * Назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::executeWorkflow()
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::callHandler()
 */
$paymentProcessor->setHandlers(array
(
    PaymentProcessor::HANDLER_SAVE_PAYMENT        => $savePayment,
    PaymentProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
    PaymentProcessor::HANDLER_SHOW_HTML           => $displayResponseHtml,
    PaymentProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedPayment
));

/**
 * Каждый вызов этого метода выполняет определенный запрос к API Paynet,
 * выбор запроса зависит от этапа обработки платежа
 *
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment::$processingStage
 * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeWorkflow()
 * @see \PaynetEasy\PaynetEasyApi\Workflow\AbstractWorkflow::processPayment()
 */
$paymentProcessor->executeWorkflow('preauth', $payment, $_REQUEST);
