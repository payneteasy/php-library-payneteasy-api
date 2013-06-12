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
            $order->getData(),
            $this->commonQueryOptions(),
            $this->createControlCode($order)
        );

        if ($order->hasRecurrentCard())
        {
            $query += $order->getRecurrentCard()->getData();
        }
        elseif ($order->hasCreditCard())
        {
            $query += $order->getCreditCard()->getData();
        }

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

        if(     !$order->hasCreditCard()
           &&   !$order->hasRecurrentCard())
        {
            throw new ConfigException('CreditCard or RecurrentCard must be defined');
        }

        $order->validate();
        $order->getCustomer()->validate();

        if ($order->hasRecurrentCard())
        {
            $order->getRecurrentCard()->validate();
        }
        elseif ($order->hasCreditCard())
        {
            $order->getCreditCard()->validate();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        if ($order->hasRecurrentCard())
        {
            return array('control' => sha1
            (
                $this->config['end_point'] .
                'CLIENT-112233' .
                '99' .
                $order->getRecurrentCard()->cardrefid() .
                $this->config['control']
            ));
        }
        elseif ($order->hasCreditCard())
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
}