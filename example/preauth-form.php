<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Первый этап обработки платежа.
 * Создание нового платежа, выполнение запроса preauth-form.
 */
if (!isset($_GET['stage']))
{
    /**
     * Создадим новый платеж
     *
     * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
     * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::$requestFieldsDefinition
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     * @see functions.php, $getConfig()
     */
    $payment = new Payment(array
    (
        'client_payment_id'     => 'CLIENT-112244',
        'description'           => 'This is test payment',
        'amount'                =>  9.99,
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
        'query_config'          =>  $getConfig()
    ));

    /**
     * Выполним запрос preauth-form
     *
     * @see \PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
     * @see \PaynetEasy\PaynetEasyApi\Query\FormQuery::updatePaymentOnSuccess()
     */
    $getPaymentProcessor()->executeQuery('preauth-form', $payment);
}
/**
 * Второй этап обработки платежа.
 * Обработка возврата пользователя от PaynetEasy
 */
elseif ($_GET['stage'] == 'processCustomerReturn' || $_GET['stage'] == 'processPaynetEasyCallback')
{
    /**
     * Обработаем данные, полученные от PaynetEasy
     */
    $getPaymentProcessor()->executeCallback(new CallbackResponse($_REQUEST), $loadPayment());
}