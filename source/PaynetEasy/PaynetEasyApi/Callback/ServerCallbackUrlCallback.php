<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
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
        Payment::STATUS_PROCESSING,
        Payment::STATUS_APPROVED,
        Payment::STATUS_DECLINED,
        Payment::STATUS_FILTERED,
        Payment::STATUS_ERROR
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
    public function __construct()
    {
        $this->validateCallbackDefinition();
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
