<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса capture.
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Capture_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\CaptureQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment = new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'paynet_payment_id'     =>  1969596,
        'query_config'          =>  $getConfig()
    ));

    /**
     * Выполним запрос capture
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\CaptureQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('capture', $payment);
}
/**
 * Второй этап обработки платежа.
 * Ожидание изменения статуса платежа.
 */
elseif ($_GET['stage'] == 'updateStatus')
{
    /**
     * Запросим статус платежа
     */
    $getPaymentProcessor()->executeQuery('status', $loadPayment());
}