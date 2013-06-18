<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use RuntimeException;

class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query = array_merge
        (
            $order->getData(),
            $order->getRecurrentCardFrom()->getData(),
            $this->commonQueryOptions(),
            $this->createControlCode($order)
        );

        if($order->getCancelReason())
        {
            $query['comment']       = $order->getCancelReason();
        }

        return $this->wrapToRequest($query);
    }

    /**
     * {@inheritdoc}
     */
    public function validateOrder(OrderInterface $order)
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
        return array('control' => sha1
        (
            $this->config['end_point'].
            $order->getOrderCode().
            $order->getAmountInCents().
            $order->getRecurrentCardFrom()->getCardRefId().
            $this->config['control']
        ));
    }
}