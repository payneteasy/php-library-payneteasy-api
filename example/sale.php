<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса sale
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Sale_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     */
    $payment = new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'description'           => 'This is test payment',
        'amount'                =>  0.99,
        'currency'              => 'USD'
    ));

    /**
     * Установим конфигурацию для выполнения запроса
     *
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment->setQueryConfig($getConfig());

    /**
     * Для этого запроса необходимо передать данные клиента
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Sale_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::$requestFieldsDefinition
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
     * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Sale_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     */
    $payment->setBillingAddress(new BillingAddress(array
    (
        'country'               => 'US',
        'state'                 => 'TX',
        'city'                  => 'Houston',
        'first_line'            => '2704 Colonial Drive',
        'zip_code'              => '1235',
        'phone'                 => '660-485-6353'
    )));

    /**
     * Для этого запроса необходимо передать данные кредитной карты
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Sale_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::$requestFieldsDefinition
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
     * Выполним запрос sale
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\SaleQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('sale', $payment);
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
/**
 * Третий этап обработки платежа.
 * Обработка возврата пользователя от PaynetEasy, если была проведена 3D-авторизация
 */
elseif ($_GET['stage'] == 'processCustomerReturn' || $_GET['stage'] == 'processPaynetEasyCallback')
{
    /**
     * Обработаем данные, полученные от PaynetEasy
     */
    $getPaymentProcessor()->executeCallback(new CallbackResponse($_REQUEST), $loadPayment());
}