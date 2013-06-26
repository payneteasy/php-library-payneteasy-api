<?php

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала
 * получить id кредитной карты, выполнив запрос create-card-ref
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 * @see \PaynetEasy\Paynet\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array());

/**
 * Для этого запроса необходимо передать ID кредитной карты
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 * @see \PaynetEasy\Paynet\Query\GetCardInfoQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\RecurrentCard
 */
$order->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058)));

$orderProcessor = new OrderProcessor('https://qa.clubber.me/paynet/api/v2/');

/**
 * Вызов этого метода заполнит поля объекта RecurrentCard, размещенного в объекте Order
 *
 * @see \PaynetEasy\Paynet\Query\GetCardInfoQuery::updateOrderOnSuccess()
 */
$orderProcessor->executeQuery('get-card-info', $getConfig(), $order, $_REQUEST);

/**
 * Сохраним заказ и выведем его на экран
 */
$saveOrder($order);
$displayEndedOrder($order);
