<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Queries\Status;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Callbacks\Redirect3D;

use PaynetEasy\Paynet\Exceptions\PaynetException;
use PaynetEasy\Paynet\Exceptions\ConfigException;
use Exception;

/**
 * The implementation of the query SALE
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#General_Sale_Process_Flow
 */
class Sale extends AbstractWorkflow
{
    public function validate()
    {
        $this->validateConfig();

        if(!$this->getOrder())
        {
            throw new ConfigException('Order is not defined');
        }

        if(!$this->getOrder()->hasCustomer())
        {
            throw new ConfigException('Customer is not defined');
        }

        if(!$this->getOrder()->hasCreditCard())
        {
            throw new ConfigException('Card is not defined');
        }

        $this->getOrder()->validate();
        $this->getOrder()->getCustomer()->validate();
        $this->getOrder()->getCreditCard()->validate();
    }

    /**
     * Processing Sale
     *
     * @param       array       $data
     *
     * @return      \PaynetEasy\Paynet\Transport\Response
     *
     * @throws      PaynetException
     */
    public function createRequest($data = null)
    {
        switch($this->state())
        {
            case self::STATE_NULL:
            case self::STATE_INIT:
            {
                $this->state        = self::STATE_PROCESSING;

                $this->validate();
                return $this->initQuery();
            }
            case self::STATE_PROCESSING:
            case self::STATE_WAIT:
            {
                return $this->statusQuery();
            }
            case self::STATE_REDIRECT:
            {
                $this->state        = self::STATE_WAIT;

                if(!is_array($data))
                {
                    throw new PaynetException('Data parameter undefined for state = STATE_REDIRECT');
                }

                return $this->redirectCalback($data);
            }
            case self::STATE_END:
            {
                return null;
            }
            default:
            {
                throw new PaynetException('Undefined state = '.$this->state());
            }
        }
    }

    protected function createControlCode()
    {
        return sha1
        (
            $this->config['end_point'].
            $this->getOrder()->getOrderCode().
            $this->getOrder()->getAmountInCents().
            $this->getOrder()->getCustomer()->getEmail().
            $this->config['control']
        );
    }

    protected function initQuery()
    {
        return $this->sendQuery
        (
            array_merge
            (
                $this->getOrder()->getCustomer()->getData(),
                $this->getOrder()->getData(),
                $this->getOrder()->getCreditCard()->getData(),
                $this->commonQueryOptions(),
                array
                (
                    '.method'       => $this->method,
                    '.end_point'    => $this->config['end_point']
                )
            )
        );
    }

    protected function statusQuery()
    {
        $status_query       = new Status($this->transport);

        $status_query->setConfig($this->config);
        $status_query->setOrder($this->getOrder());

        $e                  = null;
        try
        {
            $request    = $status_query->createRequest();
            $response   = $this->transport->makeRequest($request);
            $status_query->processResponse($response);
        }
        catch(Exception $e)
        {
        }

        $this->state        = $status_query->getOrder()->getState();
        $this->status       = $status_query->getOrder()->getStatus();
        $this->error        = $status_query->getOrder()->getLastError();

        if($e instanceof Exception)
        {
            throw $e;
        }

        return $response;
    }

    /**
     * The method handles the callback after the 3D
     *
     * @param       array $data
     * @return      Response
     *
     * @throws      PaynetException
     */
    protected function redirectCalback($data)
    {
        $callback           = new Redirect3D($this->transport);

        $callback->setConfig($this->config);
        $callback->setOrder($this->getOrder());

        $e                  = null;
        try
        {
            $request    = $callback->createRequest($data);
            $response   = $callback->processResponse(new Response($request));
        }
        catch(Exception $e)
        {
        }

        $this->state        = $callback->getOrder()->getState();
        $this->status       = $callback->getOrder()->getStatus();
        $this->error        = $callback->getOrder()->getLastError();

        if($e instanceof Exception)
        {
            throw $e;
        }

        return $response;
    }

}