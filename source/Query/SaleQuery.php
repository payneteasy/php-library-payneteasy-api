<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Exception\ValidationException;

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
            throw new ValidationException('Customer is not defined');
        }

        if(!$order->hasCreditCard())
        {
            throw new ValidationException('CreditCard must be defined');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return sha1
        (
            $this->config['end_point'] .
            $order->getClientOrderId() .
            $order->getAmountInCents() .
            $order->getCustomer()->getEmail() .
            $this->config['control']
        );
    }
}