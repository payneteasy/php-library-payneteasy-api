<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;
use PaynetEasy\Paynet\OrderData\OrderInterface;

class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                        true,   '#^[\S\s]{1,128}$#i'),
        array('order_desc',         'description',                          true,   '#^[\S\s]{1,125}$#i'),
        array('amount',             'amount',                               true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',           'currency',                             true,   '#^[A-Z]{1,3}$#i'),
        array('ipaddress',          'ipAddress',                            true,   Validator::IP),
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,   '#^[\S\s]{1,20}$#i'),
        // optional
        array('comment',            'comment',                              false,  '#^[\S\s]{1,50}$#i'),
        array('cvv2',               'recurrentCardFrom.cvv2',               false,  '#^[\S\s]{1,20}$#i'),
        // generated
        array('control',             null,                                  true,    null),
        // from config
        array('login',               null,                                  true,    null),
        array('server_callback_url', null,                                  false,   null)
    );

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return sha1
        (
            $this->config['end_point'].
            $order->getClientOrderId().
            $order->getAmountInCents().
            $order->getRecurrentCardFrom()->getCardReferenceId().
            $this->config['control']
        );
    }
}