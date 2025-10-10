<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса preauth
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions#Return_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'       => new Payment(array
        (
            'client_id'     => 'CLIENT-112244',
            'paynet_id'     =>  1969589,
            'amount'        =>  9.99,
            'currency'      => 'USD',
            'comment'       => 'cancel payment',
            'status'        =>  Payment::STATUS_CAPTURE
        )),
        'query_config'  =>  $getQueryConfig()
    ));

    /**
     * Выполним запрос return
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('return', $paymentTransaction);
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
    $getPaymentProcessor()->executeQuery('status', $loadPaymentTransaction());
}
