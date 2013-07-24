<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\PaynetEasyApi\Transport;

class Request
{
    /**
     * Paynet payment API method
     *
     * @var     string
     */
    protected $apiMethod;

    /**
     * Paynet gateway endpoint
     *
     * @var     integer
     */
    protected $endPoint;

    /**
     * Paynet gateway URL
     *
     * @var     string
     */
    protected $gatewayUrl;

    /**
     * Request data fields
     *
     * @var     array
     */
    protected $requestFields;

    /**
     * @param       array       $requestFields      Request fields array
     */
    public function __construct(array $requestFields = array())
    {
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

    /**
     * Set gateway url
     *
     * @param       string      $gatewayUrl         Gateway url
     *
     * @return      self
     */
    public function setGatewayUrl($gatewayUrl)
    {
        $this->gatewayUrl = rtrim($gatewayUrl, '/');

        return $this;
    }

    /**
     * Get gateway url
     *
     * @return      string      Gateway URL
     */
    public function getGatewayUrl()
    {
        return $this->gatewayUrl;
    }

    /**
     * Set request signature
     *
     * @param       string      $sugnature        Control code
     *
     * @return      self
     */
    public function setSignature($sugnature)
    {
        $this->requestFields['control'] = $sugnature;

        return $this;
    }
}