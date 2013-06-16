<?php

namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Exceptions\ConfigException;

class SaleQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query = array_merge
        (
            $order->getCustomer()->getData(),
            $order->getCreditCard()->getData(),
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
            throw new ConfigException('Customer is not defined');
        }

        if(!$order->hasCreditCard())
        {
            throw new ConfigException('CreditCard must be defined');
        }

        $order->validate();
        $order->getCustomer()->validate();

        if ($order->hasCreditCard())
        {
            $order->getCreditCard()->validate();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return array('control' => sha1
        (
            $this->config['end_point'] .
            $order->getOrderCode() .
            $order->getAmountInCents() .
            $order->getCustomer()->getEmail() .
            $this->config['control']
        ));
    }
}