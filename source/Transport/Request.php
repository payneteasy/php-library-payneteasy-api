<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\Transport;

use ArrayObject;
use RuntimeException;

/**
 * @todo implement class
 */
class Request extends ArrayObject
{
    /**
     * Paynet payment API method
     *
     * @var string
     */
    protected $apiMethod;

    /**
     * Paynet gateway endpoint
     *
     * @var integet
     */
    protected $endPoint;

    /**
     * Set API method
     *
     * @param       string      $apiMethod      Api method
     *
     * @return      self
     */
    public function setApiMethod($apiMethod)
    {
        $this->apiMethod = $apiMethod;

        return $this;
    }

    /**
     * Get API method
     *
     * @return      string                      API method
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * Set endpoint
     *
     * @param       integer     $endPoint       Endpount
     *
     * @return      self
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = (int) $endPoint;

        return $this;
    }

    /**
     * Get endpoint
     *
     * @return      integer                     Endpoint
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * Validates request data
     *
     * @throws      \RuntimeException           Some request data missed
     */
    public function validate()
    {
        if (!$this->getApiMethod())
        {
            throw new RuntimeException('Request api method is empty');
        }

        if (!$this->getEndPoint())
        {
            throw new RuntimeException('Request endpoint is empty');
        }

        if ($this->count() === 0)
        {
            throw new RuntimeException('Request data is empty');
        }
    }
}