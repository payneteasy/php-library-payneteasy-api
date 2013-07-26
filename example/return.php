<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

require_once './common/autoload.php';
require_once './common/functions.php';

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
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     */
    $payment = new Payment(array
    (
        'client_payment_id'         => 'CLIENT-112244',
        'paynet_payment_id'         =>  1969589,
        'amount'                    =>  9.99,
        'currency'                  => 'USD',
        'comment'                   => 'cancel payment'
    ));

    /**
     * Установим конфигурацию для выполнения запроса
     *
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment->setQueryConfig($getConfig());

    /**
     * Выполним запрос return
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\ReturnQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('return', $payment);
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
