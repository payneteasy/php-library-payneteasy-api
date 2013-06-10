<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\GatewayClientInterface;
use PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query MakeRebill
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class MakeRebill extends Sale
{
    /**
     * Comment
     * @var string
     */
    protected $comment      = '';

    /**
     * Constructor
     * @param       GatewayClientInterface        $transport
     */
    public function __construct(GatewayClientInterface $transport)
    {
        parent::__construct($transport);

        $this->method       = 'make-rebill';
    }

    public function getComment()
    {
        return $this->comment;
    }

    /**
     *
     * @param string        $comment
     *
     * @return \PaynetEasy\Paynet\Queries\ReturnTransaction
     */
    public function setComment($comment)
    {
        $this->comment          = $comment;

        return $this;
    }

    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(!$this->getOrder())
        {
            throw new ConfigException('Order is not defined');
        }

        if(!$this->getOrder()->hasRecurrentCard())
        {
            throw new ConfigException('Recurrent card is not defined');
        }

        if(strlen($this->comment) > 50)
        {
            throw new ConfigException('comment is very big (over 50 chars)');
        }

        $this->getOrder()->validate();
        $this->getOrder()->getRecurrentCard()->validate();
    }

    protected function initQuery()
    {
        return $this->sendQuery
        (
            array_merge
            (
                $this->getOrder()->getData(),
                $this->getOrder()->getRecurrentCard()->getData(),
                $this->commonQueryOptions(),
                array
                (
                    'comment'       => $this->comment,
                    '.method'       => $this->method,
                    '.end_point'    => $this->config['end_point']
                )
            )
        );
    }

    protected function createControlCode()
    {
        return sha1
        (
            $this->config['end_point'].
            $this->getOrder()->getOrderCode().
            $this->getOrder()->getAmountInCents().
            $this->getOrder()->getRecurrentCard()->cardRefId().
            $this->config['control']
        );
    }
}