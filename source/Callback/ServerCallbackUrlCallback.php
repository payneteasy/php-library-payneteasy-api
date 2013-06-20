<?php
namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
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
    static protected $allowedFields = array
    (
        'status'                => true,
        'merchant_order'        => true,
        'client_orderid'        => true,
        'orderid'               => true,
        'type'                  => true,
        'amount'                => true,
        'control'               => true,

        'descriptor'            => false,
        'error_code'            => false,
        'error_message'         => false,
        'name'                  => false,
        'email'                 => false,
        'approval-code'         => false,
        'last-four-digits'      => false,
        'bin'                   => false,
        'card-type'             => false,
        'gate-partial-reversal' => false,
        'gate-partial-capture'  => false,
        'reason-code'           => false,
        'processor-rrn'         => false,
        'comment'               => false,
        'merchantdata'          => false
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
            throw new RuntimeException("Unknown callback type: {$callbackType}");
        }

        $this->callbackType = $callbackType;
    }
}
