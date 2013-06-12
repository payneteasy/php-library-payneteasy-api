<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;

use PaynetEasy\Paynet\Callbacks\Redirect3D;

use PaynetEasy\Paynet\Exceptions\PaynetException;

/**
 * The implementation of the query SALE
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#General_Sale_Process_Flow
 */
class Sale extends AbstractWorkflow
{
    protected function initQuery(OrderInterface $order)
    {
        $order->setState(OrderInterface::STATE_PROCESSING);

        return $this->executeQuery('sale', $order);
    }

    protected function statusQuery(OrderInterface $order)
    {
        return $this->executeQuery('status', $order);
    }

    /**
     * The method handles the callback after the 3D
     *
     * @param       array $data
     * @return      Response
     *
     * @throws      PaynetException
     */
    protected function redirectCallback(OrderInterface $order, $data)
    {
        $order->setState(OrderInterface::STATE_WAIT);

        $callback   = new Redirect3D($this->queryConfig);

        $request    = $callback->createRequest($order, $data);
        $response   = new Response($request);
        $callback->processResponse($order, $response);

        return $response;
    }

}