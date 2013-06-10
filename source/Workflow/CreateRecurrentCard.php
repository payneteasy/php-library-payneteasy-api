<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Data\RecurrentCard;
use PaynetEasy\Paynet\Queries\CreateCardRef;
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
        $query              = new CreateCardRef($this->transport);

        $query->setConfig($this->config);
        $query->setOrder($this->getOrder());

        $e                  = null;
        try
        {
            $request    = $query->createRequest();
            $response   = $this->transport->makeRequest($request);
            $query->processResponse($response);
        }
        catch(Exception $e)
        {
        }

        $this->state        = $query->state();
        $this->status       = $query->status();
        $this->error        = $query->getLastError();

        if($e instanceof Exception)
        {
            throw $e;
        }

        if($response->isApproved())
        {
            $this->getOrder()->setRecurrentCard(new RecurrentCard($response['cardrefid']));
        }

        return $response;
    }
}