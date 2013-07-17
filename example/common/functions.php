<?php

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * Функция возвращает конфигурацию для выполнения запросов
 *
 * @return array
 */
$getConfig = function()
{
    return array
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
    );
};

/**
 * Функция загружает заказ из сессии
 *
 * @return      OrderInterface                      Заказ
 */
$loadOrder = function()
{
    if (!empty($_SESSION['order']))
    {
        return unserialize($_SESSION['order']);
    }
};

/**
 * Функция сохраняет заказ в сессию
 *
 * @param       OrderInterface      $order          Заказ
 * @param       Response            $response       Ответ от сервера Paynet
 */
$saveOrder = function(OrderInterface $order, Response $response = null)
{
    $_SESSION['order'] = serialize($order);
};

/**
 * Функция выводит страницу с текстов "платеж обрабатывается",
 * которая автоматически обновляется через определенное время
 *
 * @see ./common/waitPage.html
 *
 * @param       OrderInterface      $order          Заказ
 * @param       Response            $response       Ответ от сервера Paynet
 */
$displayWaitPage = function(OrderInterface $order, Response $response)
{
    print file_get_contents(__DIR__ . '/common/waitPage.html');
};

/**
 * Функция выводит html, содержащийся в ответе от Paynet,
 * который переадресует пользователя на 3D-авторизацию
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#3D_Sale_transaction_diagram
 *
 * @param       OrderInterface      $order          Заказ
 * @param       Response            $response       Ответ от сервера Paynet
 */
$displayResponseHtml = function(OrderInterface $order, Response $response)
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
 * @param       OrderInterface      $order          Заказ
 * @param       Response            $response       Ответ от сервера Paynet
 */
$redirectToResponseUrl = function(OrderInterface $order, Response $response)
{
    // Переадресуем пользователя на платежную форму
    header("Location: {$response->getRedirectUrl()}");
    exit;
};

/**
 * Функция выводит статус заказа после того, как его обработка завершена
 *
 * @param       OrderInterface      $order          Заказ
 * @param       Response            $response       Ответ от сервера Paynet
 */
$displayEndedOrder = function(OrderInterface $order, Response $response = null)
{
    // платеж завершен, выводим его статус
    print "<pre>";
    print_r("Order state: {$order->getProcessingStage()}\n");
    print_r("Order status: {$order->getStatus()}\n");
    print "</pre>";

    session_destroy();
};