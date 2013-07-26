<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

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
     * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\CreditCard
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment = new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'description'           => 'This is test payment',
        'amount'                =>  0.99,
        'currency'              => 'USD',
        'customer'              => new Customer(array
        (
            'email'                 => 'vass.pupkin@example.com',
            'ip_address'            => '127.0.0.1'
        )),
        'billing_address'       => new BillingAddress(array
        (
            'country'               => 'US',
            'state'                 => 'TX',
            'city'                  => 'Houston',
            'first_line'            => '2704 Colonial Drive',
            'zip_code'              => '1235',
            'phone'                 => '660-485-6353'
        )),
        'credit_card'           => new CreditCard(array
        (
            'card_printed_name'     => 'Vasya Pupkin',
            'credit_card_number'    => '4444 5555 6666 1111',
            'expire_month'          => '12',
            'expire_year'           => '14',
            'cvv2'                  => '123'
        )),
        'query_config'          =>  $getConfig()
    ));

    /**
     * Выполним запрос preauth
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\PreauthQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('preauth', $payment);
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