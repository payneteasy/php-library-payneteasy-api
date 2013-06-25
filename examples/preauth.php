<?php

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderData\Customer;
use PaynetEasy\Paynet\OrderData\CreditCard;
use PaynetEasy\Paynet\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'paynet_order_id'           =>  1969596
));

/**
 * Для этого запроса необходимо передать данные клиента
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Customer
 */
$order->setCustomer(new Customer(array
(
    'first_name'    => 'Vasya',
    'last_name'     => 'Pupkin',
    'email'         => 'vass.pupkin@example.com',
    'address'       => '2704 Colonial Drive',
    'birthday'      => '112681',
    'city'          => 'Houston',
    'state'         => 'TX',
    'zip_code'      => '1235',
    'country'       => 'US',
    'phone'         => '660-485-6353',
    'cell_phone'    => '660-485-6353'
)));

/**
 * Для этого запроса необходимо передать данные кредитной карты
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Preauth_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\PreauthQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\CreditCard
 */
$order->setCreditCard(new CreditCard(array
(
    'card_printed_name'         => 'Vasya Pupkin',
    'credit_card_number'        => '4444 5555 6666 1111',
    'expire_month'              => '12',
    'expire_year'               => '14',
    'cvv2'                      => '123'
)));

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
$orderProcessor->executeWorkflow('preauth', $getConfig(), $order, $_REQUEST);
