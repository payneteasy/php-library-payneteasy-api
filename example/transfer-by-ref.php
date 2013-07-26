<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса transfer-by-ref
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     */
    $payment = new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'amount'                =>  9.99,
        'currency'              => 'USD',
        'ip_address'            => '127.0.0.1',
    ));

    /**
     * Установим конфигурацию для выполнения запроса
     *
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment->setQueryConfig($getConfig());

    /**
     * Для этого запроса необходимо передать данные клиента
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     */
    $payment->setCustomer(new Customer(array
    (
        'ip_address'            => '127.0.0.1'
    )));

    /**
     * Для этого запроса необходимо передать данные кредитных карт,
     * между которыми будет происходить перевод средств
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions#Money_transfer_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
     */
    $payment->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058, 'cvv2' => 123)));
    $payment->setRecurrentCardTo(new RecurrentCard(array('cardrefid' => 8059)));

    /**
     * Выполним запрос transfer-by-ref
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferByRefQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('transfer-by-ref', $payment);
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