<?php
namespace PaynetEasy\Paynet\Query;

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
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,   '#^[\S\s]{1,20}$#i'),
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
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        parent::updateOrder($order, $response);

        $order->getRecurrentCardFrom()
            ->setCardPrintedName($response['card-printed-name'])
            ->setExpireYear($response['expire-year'])
            ->setExpireMonth($response['expire-month'])
            ->setBin($response['bin'])
            ->setLastFourDigits($response['last-four-digits']);
    }

    protected function validateResponse(OrderInterface $order, Response $response)
    {
        parent::validateResponse($order, $response);

        if(!$order->getRecurrentCardFrom())
        {
            throw new ValidationException('Recurrent card must be defined in Order');
        }
    }
}