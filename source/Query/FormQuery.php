<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use RuntimeException;

class FormQuery extends AbstractQuery
{
    /**
     * Allowed form query methods
     *
     * @var array
     */
    static protected $allowedApiMethods = array
    (
        'sale-form',
        'preauth-form',
        'transfer-form'
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
    public function setApiMethod($apiMethod)
    {
        if (!in_array($apiMethod, static::$allowedApiMethods))
        {
            throw new RuntimeException("Unknown api method: {$apiMethod}");
        }

        $this->apiMethod = $apiMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query = array_merge
        (
            $order->getCustomer()->getData(),
            $order->getData(),
            $this->commonQueryOptions(),
            $this->createControlCode($order)
        );

        return $this->wrapToRequest($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        if(!$order->hasCustomer())
        {
            throw new RuntimeException('Customer is not defined');
        }

        if($order->hasCreditCard())
        {
            throw new RuntimeException('Credir Card must not be defined for Form API');
        }

        $order->validate();
        $order->getCustomer()->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return array('control' => sha1
        (
            $this->config['end_point'].
            $order->getOrderCode().
            $order->getAmountInCents().
            $order->getCustomer()->getEmail().
            $this->config['control']
        ));
    }
}