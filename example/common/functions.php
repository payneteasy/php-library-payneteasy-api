<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * Функция возвращает конфигурацию для выполнения запросов
 *
 * @return array
 */
$getConfig = function()
{
    return new QueryConfig(array
    (
        /**
         * Точка входа для аккаунта мерчанта, выдается при подключении
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Objects
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint
         */
        'end_point'             =>  253,
        /**
         * Логин мерчанта, выдается при подключении
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users
         */
        'login'                 => 'rp-merchant1',
        /**
         * Ключ мерчанта для подписывания запросов, выдается при подключении
         */
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        /**
         * URL на который пользователь будет перенаправлен после окончания запроса
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#3D_redirect
         * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect
         */
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}",
        /**
         * URL на который пользователь будет перенаправлен после окончания запроса
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks
         */
        'server_callback_url'   => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}"
    ));
};

/**
 * Функция загружает платеж из сессии
 *
 * @return      Payment        Платеж
 */
$loadPayment = function()
{
    if (!empty($_SESSION['payment']))
    {
        return unserialize($_SESSION['payment']);
    }
};

/**
 * Функция сохраняет платеж в сессию
 *
 * @param       Payment        $payment        Платеж
 * @param       Response                $response       Ответ от сервера Paynet
 */
$savePayment = function(Payment $payment, Response $response = null)
{
    $_SESSION['payment'] = serialize($payment);
};

/**
 * Функция выводит страницу с текстов "платеж обрабатывается",
 * которая автоматически обновляется через определенное время
 *
 * @see ./common/waitPage.html
 *
 * @param       Payment        $payment        Платеж
 * @param       Response                $response       Ответ от сервера Paynet
 */
$displayWaitPage = function(Payment $payment, Response $response)
{
    print file_get_contents(__DIR__ . '/common/waitPage.html');
};

/**
 * Функция выводит html, содержащийся в ответе от Paynet,
 * который переадресует пользователя на 3D-авторизацию
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#3D_Sale_transaction_diagram
 *
 * @param       Payment        $payment        Платеж
 * @param       Response                $response       Ответ от сервера Paynet
 */
$displayResponseHtml = function(Payment $payment, Response $response)
{
    // выводим полученную форму для редиректа на 3D-авторизацию
    print $response->getHtml();
};

/**
 * Функция переадресует пользователя на URL, содержащийся в ответе от Paynet.
 * который ведет на платежную форму на стороне Paynet
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#General_Payment_Form_Process_Flow
 *
 * @param       Payment        $payment        Платеж
 * @param       Response                $response       Ответ от сервера Paynet
 */
$redirectToResponseUrl = function(Payment $payment, Response $response)
{
    // Переадресуем пользователя на платежную форму
    header("Location: {$response->getRedirectUrl()}");
    exit;
};

/**
 * Функция выводит статус платежа после того, как его обработка завершена
 *
 * @param       Payment        $payment        Платеж
 * @param       Response                $response       Ответ от сервера Paynet
 */
$displayEndedPayment = function(Payment $payment, Response $response = null)
{
    // платеж завершен, выводим его статус
    print "<pre>";
    print_r("Payment state: {$payment->getProcessingStage()}\n");
    print_r("Payment status: {$payment->getStatus()}\n");
    print "</pre>";

    session_destroy();
};