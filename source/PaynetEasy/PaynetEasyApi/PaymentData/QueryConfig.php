<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

class QueryConfig extends Data
{
    /**
     * Merchant end point
     *
     * @var     integer
     */
    protected $endPoint;

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
     * @var type
     */
    protected $callbackUrl;

    /**
     * Set merchant end point
     *
     * @param       integer     $endPoint       Merchant end point
     *
     * @return      self
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;

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
     * {@inheritdoc}
     */
    protected function getPropertyByField($fieldName)
    {
        switch ($fieldName)
        {
            case 'control':
            {
                return 'signingKey';
            }
            case 'server_callback_url':
            {
                return 'callbackUrl';
            }
            default:
            {
                return parent::getPropertyByField($fieldName);
            }
        }
    }
}