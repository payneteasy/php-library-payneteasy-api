<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если платеж был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 */
$payment = $loadPayment() ?: new Payment(array
(
    'client_payment_id'     => 'CLIENT-112244',
    'description'           => 'This is test payment',
    'amount'                =>  9.99,
    'currency'              => 'USD'
));

/**
 * Установим конфигурацию для выполнения запроса
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getConfig()
 */
$payment->setQueryConfig($getConfig());

/**
 * Для этого запроса необходимо передать данные клиента
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
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
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
 */
$payment->setBillingAddress(new BillingAddress(array
(
    'country'               => 'US',
    'city'                  => 'Houston',
    'state'                 => 'TX',
    'first_line'            => '2704 Colonial Drive',
    'zip_code'              => '1235',
    'phone'                 => '660-485-6353'
)));

/**
 * Создадим обработчик платежей и назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::executeWorkflow()
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::callHandler()
 */
$paymentProcessor = new PaymentProcessor(array
(
    PaymentProcessor::HANDLER_CATCH_EXCEPTION     => $displayException,
    PaymentProcessor::HANDLER_SAVE_PAYMENT        => $savePayment,
    PaymentProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
    PaymentProcessor::HANDLER_REDIRECT            => $redirectToResponseUrl,
    PaymentProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedPayment
));

$paymentProcessor->executeWorkflow('transfer-form', $payment, $_REQUEST);
