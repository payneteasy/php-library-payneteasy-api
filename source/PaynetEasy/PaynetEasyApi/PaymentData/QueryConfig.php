<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use RuntimeException;

class QueryConfig extends Data
{
    /**
     * Execute query to PaynetEasy sandbox gateway
     */
    const GATEWAY_MODE_SANDBOX      = 'sandbox';

    /**
     * Execute query to PaynetEasy production gateway
     */
    const GATEWAY_MODE_PRODUCTION   = 'production';

    /**
     * Allowed gateway modes
     *
     * @var     array
     */
    static protected $allowedGatewayModes = array
    (
        self::GATEWAY_MODE_SANDBOX,
        self::GATEWAY_MODE_PRODUCTION
    );

    /**
     * Merchant end point
     *
     * @var     integer
     */
    protected $endPoint;

    /**
     * Merchant end points group
     *
     * @var     integer
     */
    protected $endPointGroup;

    /**
     * Merchant login
     *
     * @var     string
     */
    protected $login;

    /**
     * Merchant key for payment signing
     *
     * @var     string
     */
    protected $signingKey;

    /**
     * URL the original payment is made from
     *
     * @var     string
     */
    protected $siteUrl;

    /**
     * URL the customer will be redirected to upon completion of the transaction
     *
     * @var     string
     */
    protected $redirectUrl;

    /**
     * URL the transaction result will be sent to
     *
     * @var     string
     */
    protected $callbackUrl;

    /**
     * PaynetEasy gateway mode: sandbox or production
     *
     * @var     string
     */
    protected $gatewayMode = self::GATEWAY_MODE_SANDBOX;

    /**
     * PaynetEasy sandbox gateway URL
     *
     * @var     string
     */
    protected $gatewayUrlSandbox;

    /**
     * PaynetEasy production gateway URL
     *
     * @var     string
     */
    protected $gatewayUrlProduction;

    /**
     * Set merchant end point
     *
     * @param       integer     $endPoint       Merchant end point
     *
     * @return      self
     */
    public function setEndPoint($endPoint)
    {
        if (strlen($this->getEndPointGroup()) > 0)
        {
            throw new RuntimeException(
                "End point group has been already set. You can set either end point or end point group."
            );
        }

        $this->endPoint = (int) $endPoint;

        return $this;
    }

    /**
     * Get merchant end point
     *
     * @return      integer
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * Set merchant end point group
     *
     * @param       integer     $endPoint       Merchant end point group
     *
     * @return      self
     */
    function getEndPointGroup() {
        return $this->endPointGroup;
    }

    /**
     * Get merchant end point group
     *
     * @return      integer
     */
    function setEndPointGroup($endPointGroup) {
        if (strlen($this->getEndPoint()) > 0)
        {
            throw new RuntimeException(
                "End point has been already set. You can set either end point or end point group."
            );
        }

        $this->endPointGroup = (int) $endPointGroup;
    }

    /**
     * Set merchant login
     *
     * @param       string      $login      Merchant login
     *
     * @return      self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get merchant login
     *
     * @return      string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set merchant key for payment signing
     *
     * @param       string      $signingKey     Key for payment signing
     *
     * @return      self
     */
    public function setSigningKey($signingKey)
    {
        $this->signingKey = $signingKey;

        return $this;
    }

    /**
     * Get merchant key for payment signing
     *
     * @return      string
     */
    public function getSigningKey()
    {
        return $this->signingKey;
    }

    /**
     * Set URL the original payment is made from
     *
     * @param       string      $siteUrl            URL the original payment is made from
     *
     * @return      self
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * Get URL the original payment is made from
     *
     * @return      string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * Set URL the customer will be redirected to upon completion of the transaction
     *
     * @param       string      $redirectUrl        URL the customer will be redirected
     *
     * @return      self
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get URL the customer will be redirected to upon completion of the transaction
     *
     * @return      string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Get URL the transaction result will be sent to
     *
     * @param       string      $callbackUrl        URL the transaction result will be sent to
     *
     * @return      self
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * Get URL the transaction result will be sent to
     *
     * @return      string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Set gateway mode
     *
     * @param       string      $gatewayMode        Gateway mode: sndbox, production
     *
     * @return      self
     */
    public function setGatewayMode($gatewayMode)
    {
        $this->checkGatewayMode($gatewayMode);

        $this->gatewayMode = $gatewayMode;

        return $this;
    }

    /**
     * Get gateway mode
     *
     * @return      string
     */
    public function getGatewayMode()
    {
        return $this->gatewayMode;
    }

    /**
     * Set sandbox gateway URL
     *
     * @param       string      $gatewayUrl     Sandbox gateway url
     *
     * @return      self
     */
    public function setGatewayUrlSandbox($gatewayUrl)
    {
        $this->gatewayUrlSandbox = $gatewayUrl;

        return $this;
    }

    /**
     * Get sandbox gateway URL
     *
     * @return      string
     */
    public function getGatewayUrlSandbox()
    {
        return $this->gatewayUrlSandbox;
    }

    /**
     * Set production gateway URL
     *
     * @param       string      $gatewayUrl     Production gateway url
     *
     * @return      self
     */
    public function setGatewayUrlProduction($gatewayUrl)
    {
        $this->gatewayUrlProduction = $gatewayUrl;
    }

    /**
     * Get production gateway URL
     *
     * @return      string
     */
    public function getGatewayUrlProduction()
    {
        return $this->gatewayUrlProduction;
    }

    /**
     * Set gateway url for different gateway modes
     *
     * @param       string      $gatewayUrl         Gateway url
     * @param       string      $gatewayMode        Mode for gateway url: sandbox, production
     *
     * @return      self
     */
    public function setGatewayUrl($gatewayUrl, $gatewayMode)
    {
        $this->checkGatewayMode($gatewayMode);

        switch ($gatewayMode)
        {
            case self::GATEWAY_MODE_SANDBOX:
            {
                $this->setGatewayUrlSandbox($gatewayUrl);
                break;
            }
            case self::GATEWAY_MODE_PRODUCTION:
            {
                $this->setGatewayUrlProduction($gatewayUrl);
                break;
            }
        }

        return $this;
    }

    /**
     * Get gateway url for current gateway mode
     *
     * @return      string      Sandbox gateway url if gateway mode is sandbox,
     *                          production gateway url if gateway mode is production
     */
    public function getGatewayUrl()
    {
        switch ($this->getGatewayMode())
        {
            case self::GATEWAY_MODE_SANDBOX:
            {
                return $this->getGatewayUrlSandbox();
            }
            case self::GATEWAY_MODE_PRODUCTION:
            {
                return $this->getGatewayUrlProduction();
            }
            default:
            {
                throw new RuntimeException('You must set gatewayMode property first');
            }
        }
    }

    /**
     * Checks, is gateway mode allowed or not
     *
     * @param       string      $gatewayMode        Gateway mode
     *
     * @throws      RuntimeException                Gateway mode not allowed
     */
    protected function checkGatewayMode($gatewayMode)
    {
        if (!in_array($gatewayMode, static::$allowedGatewayModes))
        {
            throw new RuntimeException("Unknown gateway mode given: '{$gatewayMode}'");
        }
    }
}