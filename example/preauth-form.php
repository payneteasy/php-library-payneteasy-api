<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
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
    'currency'              => 'USD',
    'ipaddress'             => '127.0.0.1'
));

/**
 * Для этого запроса необходимо передать данные клиента
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
 */
$payment->setCustomer(new Customer(array
(
    'address'               => '2704 Colonial Drive',
    'city'                  => 'Houston',
    'zip_code'              => '1235',
    'country'               => 'US',
    'email'                 => 'vass.pupkin@example.com',
    'phone'                 => '660-485-6353'
)));

/**
 * Создадим обработчик платежей и передадим ему URL для доступа к платежному шлюзу
 *
 * @see \PaynetEasy\PaynetEasyApi\Transport\GatewayClient::$gatewayUrl
 */
$paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');

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
    PaymentProcessor::HANDLER_REDIRECT            => $redirectToResponseUrl,
    PaymentProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedPayment
));

$paymentProcessor->executeWorkflow('preauth-form', $getConfig(), $payment, $_REQUEST);
