<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Первый этап верификации клиента.
 * Создание нового платежа, выполнение запроса account-verification
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://doc.payneteasy.com/doc/account-verification.htm#account-verification-request-parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\AccountVerificationQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\CreditCard
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'               => new Payment(array
        (
            'client_id'             => 'CLIENT-112244',
            'description'           => 'This is test payment',
            'customer'              =>  new Customer(array
            (
                'email'                 => 'vass.pupkin@example.com',
                'ip_address'            => '127.0.0.1'
            )),
            'billing_address'       =>  new BillingAddress(array
            (
                'country'               => 'US',
                'state'                 => 'TX',
                'city'                  => 'Houston',
                'first_line'            => '2704 Colonial Drive',
                'zip_code'              => '1235',
                'phone'                 => '660-485-6353'
            )),
            'credit_card'           =>  new CreditCard(array
            (
                'card_printed_name'     => 'Vasya Pupkin',
                'credit_card_number'    => '3776 7964 0568 206',
                'expire_month'          => '12',
                'expire_year'           => '15',
                'cvv2'                  => '123'
            ))
        )),
        'query_config'          =>  $getQueryConfig()
    ));

    /**
     * Выполним запрос account-verification
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\AccountVerificationQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('account-verification', $paymentTransaction);
}
/**
 * Второй этап верификации клиента.
 * Ожидание изменения статуса платежа.
 */
elseif ($_GET['stage'] == 'updateStatus')
{
    /**
     * Запросим статус платежа
     */
    $getPaymentProcessor()->executeQuery('status', $loadPaymentTransaction());
}
