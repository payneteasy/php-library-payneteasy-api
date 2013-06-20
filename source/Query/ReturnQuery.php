<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                    true,   '#^[\S\s]{1,128}$#i'),
        array('orderid',            'paynetOrderId',                    true,   '#^[\S\s]{1,20}$#i'),
        array('amount',             'amount',                           true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',           'currency',                         true,   '#^[A-Z]{1,3}$#i'),
        array('comment',            'comment',                          true,  '#^[\S\s]{1,50}$#i'),
        // generated
        array('control',             null,                              true,    null),
        // from config
        array('login',               null,                              true,    null)
    );

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // Checksum used to ensure that it is Merchant (and not a fraudster)
        // that initiates the return request.
        // This is SHA-1 checksum of the concatenation login + client_orderid + orderid + merchant-control
        // if amount is not specified,
        // and login + client_orderid + orderid + amount_in_cents +
        // currency + merchant-control if amount is specified
        $sign = array
        (
            $this->config['login'],
            $order->getClientOrderId(),
            $order->getPaynetOrderId()
        );

        if($order->getAmount())
        {
            $sign[] = $order->getAmountInCents();
            $sign[] = $order->getCurrency();
        }

        $sign[] = $this->config['control'];

        return sha1(implode('', $sign));
    }
}