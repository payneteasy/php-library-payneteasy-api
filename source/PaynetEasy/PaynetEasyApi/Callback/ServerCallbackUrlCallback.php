<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;
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
        PaymentInterface::STATUS_PROCESSING,
        PaymentInterface::STATUS_APPROVED,
        PaymentInterface::STATUS_DECLINED,
        PaymentInterface::STATUS_FILTERED,
        PaymentInterface::STATUS_ERROR
    );

    /**
     * {@inheritdoc}
     */
    static protected $callbackFieldsDefinition = array
    (
        array('orderid',        'paynetPaymentId'),
        array('merchant_order', 'clientPaymentId'),
        array('client_orderid', 'clientPaymentId'),
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
