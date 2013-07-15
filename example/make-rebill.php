<?php

use PaynetEasy\PaynetEasyApi\OrderData\Order;
use PaynetEasy\PaynetEasyApi\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Обратите внимание, что для выполнения этого запроса необходимо сначала
 * получить id кредитной карты, выполнив запрос create-card-ref
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Initial_Payment
 *
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'order_desc'                => 'This is test order',
    'amount'                    =>  0.99,
    'currency'                  => 'USD',
    'ipaddress'                 => '127.0.0.1'
));

/**
 * Для этого запроса необходимо передать ID кредитной карты
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Recurrent_Payment_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\MakeRebillQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\OrderData\RecurrentCard
 */
$order->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => 8058)));

$orderProcessor = new OrderProcessor('https://payment.domain.com/paynet/api/v2/');

/**
 * Назначим обработчики для разных событий, происходящих при обработке платежа
 *
 * @see ./common/functions.php
 * @see PaynetEasy\PaynetEasyApi\OrderProcessor::executeWorkflow()
 * @see PaynetEasy\PaynetEasyApi\OrderProcessor::callHandler()
 */
$orderProcessor->setHandlers(array
(
    OrderProcessor::HANDLER_SAVE_ORDER          => $saveOrder,
    OrderProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
    OrderProcessor::HANDLER_SHOW_HTML           => $displayResponseHtml,
    OrderProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedOrder
));

/**
 * Каждый вызов этого метода выполняет определенный запрос к API Paynet,
 * выбор запроса зависит от этапа обработки заказа
 *
 * @see \PaynetEasy\PaynetEasyApi\OrderData\Order::$transportStage
 * @see \PaynetEasy\PaynetEasyApi\OrderProcessor::executeWorkflow()
 * @see \PaynetEasy\PaynetEasyApi\Workflow\AbstractWorkflow::processOrder()
 */
$orderProcessor->executeWorkflow('make-rebill', $getConfig(), $order, $_REQUEST);
