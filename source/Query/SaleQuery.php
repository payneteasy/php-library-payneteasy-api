<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use RuntimeException;

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
            throw new RuntimeException('Customer is not defined');
        }

        if(!$order->hasCreditCard())
        {
            throw new RuntimeException('CreditCard must be defined');
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