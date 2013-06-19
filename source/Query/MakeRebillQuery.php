<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Exception\ValidationException;

class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        $recurrentCard = $order->getRecurrentCardFrom();

        $query = array_merge
        (
            $order->getData(),
            $this->commonQueryOptions(),
            array
            (
                'cardrefid' => $recurrentCard->getCardRefId(),
                'cvv2'      => $recurrentCard->getCvv2()
            )
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
            throw new ValidationException('Recurrent card is not defined');
        }

        $recurrentCard = $order->getRecurrentCardFrom();

        if (!$recurrentCard->getCvv2())
        {
            throw new ValidationException('Recurrent card CVV2 is not defined');
        }

        if (!$recurrentCard->getCardRefId())
        {
            throw new ValidationException('Recurrent card reference ID is not defined');
        }

        $order->validate();
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