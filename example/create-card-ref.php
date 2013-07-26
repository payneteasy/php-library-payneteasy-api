<?php

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

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
 * Создадим новый платеж.
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_registration_request_parameters
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::$requestFieldsDefinition
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\Payment
 * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
 * @see functions.php, $getConfig()
 */
$payment = new Payment(array
(
    'client_payment_id'     => 'CLIENT-112244',
    'paynet_payment_id'     =>  1969595,
    'processing_stage'      =>  Payment::STAGE_FINISHED,
    'status'                =>  Payment::STATUS_APPROVED,
    'query_config'          =>  $getConfig()
));

/**
 * Вызов этого метода создаст в объекте Payment объект RecurrentCard
 *
 * @see \PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery::updatePaymentOnSuccess()
 */
$getPaymentProcessor()->executeQuery('create-card-ref', $payment);