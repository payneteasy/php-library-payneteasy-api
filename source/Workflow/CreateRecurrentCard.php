<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;

/**
 * The implementation of the Reccurent Transaction init
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions
 */
class CreateRecurrentCard extends Sale
{
    /**
     * {@inheritdoc}
     */
    public function processOrder(OrderInterface $order, array $callbackData = array())
    {
        $response       = parent::processOrder($order, $callbackData);

        if($response->isApproved())
        {
            $this->createCardRef($order);
        }

        return $response;
    }

    protected function createCardRef(OrderInterface $order)
    {
        return $this->executeQuery('create-card-ref', $order);
    }
}