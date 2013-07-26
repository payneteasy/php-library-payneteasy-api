<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса make-rebill.
 */
if (!isset($_GET['stage']))
{
    /**
     * Обратите внимание, что для выполнения этого запроса необходимо сначала
     * получить id кредитной карты, выполнив запрос create-card-ref
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
     *
     * Создадим новый платеж.
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     */
    $payment = $loadPayment() ?: new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'description'           => 'This is test payment',
        'amount'                =>  0.99,
        'currency'              => 'USD',
        'ip_address'            => '127.0.0.1'
    ));

    /**
     * Установим конфигурацию для выполнения запроса
     *
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment->setQueryConfig($getConfig());

    /**
     * Для этого запроса необходимо передать данные клиента
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     */
    $payment->setCustomer(new Customer(array
    (
        'ip_address'            => '127.0.0.1'
    )));

    /**
     * Для этого запроса необходимо передать ID кредитной карты
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
     */
    $payment->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058)));

    /**
     * Выполним запрос make-rebill
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('make-rebill', $payment);
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