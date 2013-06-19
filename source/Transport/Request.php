<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\Transport;

use ArrayObject;

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
}