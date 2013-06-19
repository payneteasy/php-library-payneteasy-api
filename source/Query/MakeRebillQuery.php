<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use RuntimeException;

class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        $query = array_merge
        (
            $order->getData(),
            $order->getRecurrentCardFrom()->getData(),
            $this->commonQueryOptions($order)
        );

        if($order->getCancelReason())
        {
            $query['comment'] = $order->getCancelReason();
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        if(!$order->hasRecurrentCardFrom())
        {
            throw new RuntimeException('Recurrent card is not defined');
        }

        $order->validate();
        $order->getRecurrentCardFrom()->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        return sha1
        (
            $this->config['end_point'].
            $order->getOrderCode().
            $order->getAmountInCents().
            $order->getRecurrentCardFrom()->getCardRefId().
            $this->config['control']
        );
    }
}