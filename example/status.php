<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала
 * выполнить любой запрос, который подразумевает асинхронную обработку:
 * sale, preauth, capture, transfer-by-ref, make-rebill, return
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions
 * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 *
 * Если платеж был сохранен - получим его сохраненную версию, иначе создадим новый.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Payment_status_call_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\StatusQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 */
$payment = $loadPayment() ?: new Payment(array
(
    'client_payment_id'     => 'CLIENT-112244',
    'paynet_payment_id'     =>  1969595
));

/**
 * Установим конфигурацию для выполнения запроса
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\StatusQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getConfig()
 */
$payment->setQueryConfig($getConfig());

/**
 * Вызов этого метода обновит статус обработки платежа
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\Status::updatePaymentOnSuccess()
 */
$getPaymentProcessor()->executeQuery('status', $payment);