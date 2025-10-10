<?php
namespace PaynetEasy\PaynetEasyApi\Transport;

use ArrayObject;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;
use RuntimeException;

class Response extends ArrayObject
{
    /**
     * Need to update payment status
     */
    const NEEDED_STATUS_UPDATE  = 'status_update';

    /**
     * Need to show html from response
     */
    const NEEDED_SHOW_HTML      = 'show_html';

    /**
     * Need redirect to response url
     */
    const NEEDED_REDIRECT       = 'redirect';

    /**
     * Allowed needed actions list
     *
     * @var array
     */
    static protected $allowedNeededActions = array
    (
        self::NEEDED_STATUS_UPDATE,
        self::NEEDED_SHOW_HTML,
        self::NEEDED_REDIRECT
    );

    /**
     * Action needed after API method request ended
     *
     * @var string
     */
    protected $neededAction;

    public function __construct($response = array())
    {
        parent::__construct(array_map('trim', $response));
    }

    /**
     * Set action needed after API method request ended
     *
     * @param       string      $neededAction           Action needed after API method request ended
     *
     * @return      self
     */
    public function setNeededAction($neededAction)
    {
        if (!in_array($neededAction, static::$allowedNeededActions))
        {
            throw new RuntimeException("Unknown needed action: '{$neededAction}'");
        }

        $this->neededAction = $neededAction;

        return $this;
    }

    /**
     * Get action needed after API method request ended
     *
     * @return      string
     */
    public function getNeededAction()
    {
        return $this->neededAction;
    }

    /**
     * True if Response has html to display
     *
     * @return      boolean
     */
    public function hasHtml()
    {
        return strlen($this->getHtml()) > 0;
    }

    /**
     * True if Response has url for redirect
     *
     * @return      boolean
     */
    public function hasRedirectUrl()
    {
        return strlen($this->getRedirectUrl()) > 0;
    }

    /**
     * True if status update needed
     *
     * @return      boolean
     */
    public function isStatusUpdateNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_STATUS_UPDATE;
    }

    /**
     * True if html show needed
     *
     * @return      boolean
     */
    public function isShowHtmlNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_SHOW_HTML;
    }

    /**
     * True if redirect needed
     *
     * @return      boolean
     */
    public function isRedirectNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_REDIRECT;
    }

    /**
     * Get response html
     *
     * @return      string
     */
    public function getHtml()
    {
        return $this->getValue('html');
    }

    /**
     * Get response type
     *
     * @return      string
     */
    public function getType()
    {
        return strtolower($this->getValue('type'));
    }

    /**
     * Get response status
     *
     * @return      string
     */
    public function getStatus()
    {
        if (   !strlen($this->getValue('status'))
            && !in_array($this->getType(), array('validation-error', 'error')))
        {
            $this->offsetSet('status', 'processing');
        }

        return strtolower($this->getValue('status'));
    }

    /**
     * Get payment client payment id
     *
     * @return      string
     */
    public function getPaymentClientId()
    {
        return $this->getAnyKey(array('merchant-order-id', 'client_orderid', 'merchant_order'));
    }

    /**
     * Get payment PaynetEasy payment id
     *
     * @return      string
     */
    public function getPaymentPaynetId()
    {
        return $this->getAnyKey(array('orderid', 'paynet-order-id'));
    }

    /**
     * Get payment card reference id
     *
     * @return      string
     */
    public function getCardPaynetId()
    {
        return $this->getValue('card-ref-id');
    }

    /**
     * Get response redirect url
     *
     * @return      string
     */
    public function getRedirectUrl()
    {
        return $this->getValue('redirect-url');
    }

    /**
     * Get response control code
     *
     * @return      string
     */
    public function getControlCode()
    {
        return $this->getAnyKey(array('control', 'merchant_control'));
    }

    /**
     * Get response error message
     *
     * @return      string
     */
    public function getErrorMessage()
    {
        return $this->getAnyKey(array('error_message', 'error-message'));
    }

    /**
     * Get response error code
     *
     * @return      integer
     */
    public function getErrorCode()
    {
        return $this->getAnyKey(array('error_code', 'error-code'));
    }

    /**
     * True, if response has some kind of error
     *
     * @return      boolean
     */
    public function isError()
    {
        return    in_array($this->getType(), array('validation-error', 'error'))
               || $this->getStatus() == 'error';
    }

    /**
     * True, if response is approved
     *
     * @return      boolean
     */
    public function isApproved()
    {
        return $this->getStatus() === 'approved';
    }

    /**
     * True, if response is processing
     *
     * @return      boolean
     */
    public function isProcessing()
    {
        return $this->getStatus() === 'processing';
    }

    /**
     * True, if response is filtered or declined
     *
     * @return      boolean
     */
    public function isDeclined()
    {
        return in_array($this->getStatus(), array('filtered', 'declined'));
    }

    /**
     * Get response error as Exception instance
     *
     * @return      \PaynetEasy\PaynetEasyApi\Exception\PaynetException        Response error
     *
     * @throws      \RuntimeException                                   Response has no error
     */
    public function getError()
    {
        if($this->isError() || $this->isDeclined())
        {
            return new PaynetException($this->getErrorMessage(), $this->getErrorCode());
        }
        else
        {
            throw new RuntimeException('Response has no error');
        }
    }

    /**
     * Get value of first key that exists in Response
     *
     * @param       array                   $keys       Given keys
     *
     * @return      string|integer|null                 Value of first found key or null if all keys does not exists
     */
    protected function getAnyKey(array $keys)
    {
        foreach($keys as $key)
        {
            $value = $this->getValue($key);

            if(!is_null($value))
            {
                return $value;
            }
        }
    }

    /**
     * Method get value by index
     * without warning if index does not exists
     *
     * @param       string                  $index      Index
     *
     * @return      string|integer|null                 Index value or null if index not exists
     */
    protected function getValue($index)
    {
        if($this->offsetExists($index))
        {
            return $this->offsetGet($index);
        }
    }
}