<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

require_once __DIR__ . '/common/autoload.php';
require_once __DIR__ . '/common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса transfer-form.
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferFormQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see common/functions.php, $getQueryConfig()
     */
    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'               => new Payment(array
        (
            'client_id'             => 'CLIENT-112244',
            'description'           => 'This is test payment',
            'amount'                =>  9.99,
            'currency'              => 'USD',
            'customer'              =>  new Customer(array
            (
                'email'                 => 'vass.pupkin@example.com',
                'ip_address'            => '127.0.0.1'
            )),
            'billing_address'       =>  new BillingAddress(array
            (
                'country'               => 'US',
                'city'                  => 'Houston',
                'state'                 => 'TX',
                'first_line'            => '2704 Colonial Drive',
                'zip_code'              => '1235',
                'phone'                 => '660-485-6353'
            ))
        )),
        'query_config'          =>  $getQueryConfig()
    ));

    /**
     * Выполним запрос transfer-form
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\TransferFormQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('transfer-form', $paymentTransaction);
}
/**
 * Второй этап обработки платежа.
 * Обработка возврата пользователя от PaynetEasy
 */
elseif ($_GET['stage'] == 'processCustomerReturn')
{
    /**
     * Обработаем данные, полученные от PaynetEasy
     */
    $getPaymentProcessor()->processCustomerReturn(new CallbackResponse($_POST), $loadPaymentTransaction());
}
/**
 * Дополнительный этап обработки платежа.
 * Обработка коллбэка от PaynetEasy.
 */
elseif ($_GET['stage'] == 'processPaynetEasyCallback')
{
    /**
     * Обработаем данные, полученные от PaynetEasy
     */
    $getPaymentProcessor()->processPaynetEasyCallback(new CallbackResponse($_GET), $loadPaymentTransaction());
}