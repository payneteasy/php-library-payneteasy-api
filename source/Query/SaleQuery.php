<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use RuntimeException;

class SaleQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getCustomer()->getData(),
            $order->getCreditCard()->getData(),
            $order->getData(),
            $this->commonQueryOptions($order)
        );
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

        if(!$order->hasCreditCard())
        {
            throw new RuntimeException('CreditCard must be defined');
        }

        $order->validate();
        $order->getCustomer()->validate();
        $order->getCreditCard()->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return sha1
        (
            $this->config['end_point'] .
            $order->getOrderCode() .
            $order->getAmountInCents() .
            $order->getCustomer()->getEmail() .
            $this->config['control']
        );
    }
}