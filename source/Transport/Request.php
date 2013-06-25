<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\Transport;

use RuntimeException;

class Request
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
     * Request data fields
     *
     * @var array
     */
    protected $requestFields;

    /**
     * @param       array       $requestFields      Request fields array
     */
    public function __construct(array $requestFields)
    {
        if (empty($requestFields))
        {
            throw new RuntimeException('Request fields can not be empty');
        }

        $this->requestFields = $requestFields;
    }

    /**
     * Get request fields array
     *
     * @return      array
     */
    public function getRequestFields()
    {
        return $this->requestFields;
    }

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