<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

class TransferByRefQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',             'clientOrderId',                        true,   '#^[\S\s]{1,128}$#i'),
        array('amount',                     'amount',                               true,   '#^[0-9\.]{1,11}$#i'),
        array('currency',                   'currency',                             true,   '#^[A-Z]{1,3}$#i'),
        array('ipaddress',                  'ipAddress',                            true,   Validator::IP),
        array('destination-card-ref-id',    'recurrentCardFrom.cardReferenceId',    true,   '#^[\S\s]{1,20}$#i'),
        // optional
        array('order_desc',                 'description',                          false,  '#^[\S\s]{1,125}$#i'),
        array('source-card-ref-id',         'recurrentCardFrom.cardReferenceId',    false,  '#^[\S\s]{1,20}$#i'),
        array('cvv2',                       'recurrentCardFrom.cvv2',               false,  '#^[\S\s]{1,20}$#i'),
        // generated
        array('control',                    null,                                   true,    null),
        // from config
        array('login',                      null,                                   true,    null),
        array('redirect_url',               null,                                   false,   null),
        array('server_callback_url',        null,                                   false,   null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientOrderId',
        'recurrentCardFrom.cardReferenceId',
        'recurrentCardTo.cardReferenceId',
        'amountInCents',
        'currency',
        'control'
    );
}