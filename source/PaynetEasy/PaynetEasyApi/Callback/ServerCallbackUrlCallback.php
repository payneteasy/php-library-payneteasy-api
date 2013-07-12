<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use RuntimeException;

class ServerCallbackUrlCallback extends AbstractCallback
{
    /**
     * Allowed callback types
     *
     * @var array
     */
    static protected $allowedCallbackTypes = array
    (
        'sale',
        'reversal',
        'chargeback'
    );

    /**
     * {@inheritdoc}
     */
    static protected $allowedStatuses = array
    (
        OrderInterface::STATUS_PROCESSING,
        OrderInterface::STATUS_APPROVED,
        OrderInterface::STATUS_DECLINED,
        OrderInterface::STATUS_FILTERED,
        OrderInterface::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'paynetOrderId'),
        array('merchant_order', 'clientOrderId'),
        array('client_orderid', 'clientOrderId'),
        array('amount',         'amount'),
        array('status',          null),
        array('type',            null),
        array('control',         null)
    );

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }

    /**
     * Indirectly sets gateway API query method
     *
     * @param       string      $apiMethod      Gateway API method
     */
    public function setCallbackType($callbackType)
    {
        if (!in_array($callbackType, static::$allowedCallbackTypes))
        {
            throw new RuntimeException("Unknown callback type: '{$callbackType}'");
        }

        $this->callbackType = $callbackType;
    }
}
