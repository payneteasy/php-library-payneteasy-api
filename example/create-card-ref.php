<?php

use PaynetEasy\PaynetEasyApi\OrderData\Order;
use PaynetEasy\PaynetEasyApi\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала провести
 * платеж одним из следующих способов: sale, preauth, sale-form, preauth-form
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_registration_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'paynet_order_id'           =>  1969595
));

/**
 * Платеж обязательно должен быть успешно завершен
 */
$order->setTransportStage(Order::STAGE_ENDED);
$order->setStatus(Order::STATUS_APPROVED);

$orderProcessor = new OrderProcessor('https://payment.domain.com/paynet/api/v2/');

/**
 * Вызов этого метода создаст в объекте Order объект RecurrentCard
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::updateOrderOnSuccess()
 */
$orderProcessor->executeQuery('create-card-ref', $getConfig(), $order, $_REQUEST);

/**
 * Сохраним заказ и выведем его на экран
 */
$saveOrder($order);
$displayEndedOrder($order);
