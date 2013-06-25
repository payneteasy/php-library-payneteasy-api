<?php

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions#Return_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\ReturnQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'paynet_order_id'           =>  1969589,
    'amount'                    =>  9.99,
    'currency'                  => 'USD',
    'comment'                   => 'cancel order'
));

/**
 * Создадим обработчик платежей и передадим ему URL для доступа к платежному шлюзу
 *
 * @see \PaynetEasy\Paynet\Transport\GatewayClient::$gatewayUrl
 */
$orderProcessor = new OrderProcessor('https://qa.clubber.me/paynet/api/v2/');

/**
 * Назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\Paynet\OrderProcessor::executeWorkflow()
 * @see PaynetEasy\Paynet\OrderProcessor::fireEvent()
 */
$orderProcessor->setEventListeners(array
(
    OrderProcessor::EVENT_ORDER_CHANGED         => $saveOrder,
    OrderProcessor::EVENT_STATUS_NOT_CHANGED    => $displayWaitPage,
    OrderProcessor::EVENT_HTML_RECEIVED         => $displayResponseHtml,
    OrderProcessor::EVENT_PROCESSING_ENDED      => $displayEndedOrder
));

/**
 * Каждый вызов этого метода выполняет определенный запрос к API Paynet,
 * выбор запроса зависит от этапа обработки заказа
 *
 * @see \PaynetEasy\Paynet\OrderData\Order::$transportStage
 * @see \PaynetEasy\Paynet\OrderProcessor::executeWorkflow()
 * @see \PaynetEasy\Paynet\Workflow\AbstractWorkflow::processOrder()
 */
$orderProcessor->executeWorkflow('return', $getConfig(), $order, $_REQUEST);
