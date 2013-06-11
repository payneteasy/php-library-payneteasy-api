<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\RecurrentCard;
use PaynetEasy\Paynet\Queries\CreateCardRefQuery;
use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use Exception;

/**
 * The implementation of the Reccurent Transaction init
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions
 */
class CreateRecurrentCard extends Sale
{
    /**
     * Constructor
     * @param       GatewayClientInterface        $transport
     */
    public function __construct(GatewayClientInterface $transport)
    {
        parent::__construct($transport);

        $this->method       = 'sale';
    }

    public function createRequest($data = null)
    {
        $response       = parent::createRequest($data);

        if($response->isApproved())
        {
            $this->createCardRef();
        }

        return      $response;
    }

    protected function createCardRef()
    {
        $order = $this->getOrder();
        $query = new CreateCardRefQuery($this->config);

        $e                  = null;
        try
        {
            $request    = $query->createRequest($order);
            $response   = $this->transport->makeRequest($request);
            $query->processResponse($order, $response);
        }
        catch(Exception $e)
        {
        }

        $this->state        = $order->getState();
        $this->status       = $order->getStatus();
        $this->error        = $order->getLastError();

        if($e instanceof Exception)
        {
            throw $e;
        }

        if($response->isApproved())
        {
            $order->setRecurrentCard(new RecurrentCard($response['cardrefid']));
        }

        return $response;
    }
}