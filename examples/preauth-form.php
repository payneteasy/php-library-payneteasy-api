<?php

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderData\Customer;
use PaynetEasy\Paynet\OrderProcessor;

require_once './common/autoload.php';
require_once './common/functions.php';

session_start();

/**
 * Если заказ был сохранен - получим его сохраненную версию, иначе создадим новый
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\FormQuery::$requestFieldsDefinition
 * @see \PaynetEasy\Paynet\OrderData\Order
 */
$order = $loadOrder() ?: new Order(array
(
    'client_orderid'            => 'CLIENT-112244',
    'desc'                      => 'This is test order',
    'amount'                    =>  9.99,
    'currency'                  => 'USD',
    'ipaddress'                 => '127.0.0.1',
    'site_url'                  => 'http://example.com'
));

/**
 * Для этого запроса необходимо передать данные клиента
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_Request_Parameters
 * @see \PaynetEasy\Paynet\Query\FormQuery::$requestFieldsDefinition
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
 * @see PaynetEasy\Paynet\OrderProcessor::callHandler()
 */
$orderProcessor->setHandlers(array
(
    OrderProcessor::HANDLER_SAVE_ORDER          => $saveOrder,
    OrderProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
    OrderProcessor::HANDLER_REDIRECT            => $redirectToResponseUrl,
    OrderProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedOrder
));

$orderProcessor->executeWorkflow('preauth-form', $getConfig(), $order, $_REQUEST);
