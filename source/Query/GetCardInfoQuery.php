<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;
use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfoQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        // generated
        array('control',             null,                                  true,    null),
        // from config
        array('login',               null,                                  true,    null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'recurrentCardFrom.cardReferenceId',
        'control'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'card-printed-name',
        'expire-year',
        'expire-month',
        'bin',
        'last-four-digits',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-response';
    static protected $successResponseType = 'get-card-info-response';

    /**
     * {@inheritdoc}
     */
    protected function updateOrderOnSuccess(OrderInterface $order, Response $response)
    {
        parent::updateOrderOnSuccess($order, $response);

        $order->getRecurrentCardFrom()
            ->setCardPrintedName($response['card-printed-name'])
            ->setExpireYear($response['expire-year'])
            ->setExpireMonth($response['expire-month'])
            ->setBin($response['bin'])
            ->setLastFourDigits($response['last-four-digits']);
    }

    protected function validateResponseOnSuccess(OrderInterface $order, Response $response)
    {
        parent::validateResponseOnSuccess($order, $response);

        if(!$order->getRecurrentCardFrom())
        {
            throw new ValidationException('Recurrent card must be defined in Order');
        }
    }
}