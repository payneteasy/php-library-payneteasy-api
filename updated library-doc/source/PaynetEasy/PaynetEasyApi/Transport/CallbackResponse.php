<?php
namespace PaynetEasy\PaynetEasyApi\Transport;

use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * Merchant Callback
 * Sale, Return, Chargeback callback simple URL
 *
 * see: http://wiki.payneteasy.com/index.php/PnE:Merchant_Callback#Sale.2C_Return_Callback_Parameters
 */
class CallbackResponse extends Response
{
    /**
     * Get payment amount
     *
     * @return      float
     */
    public function getAmount()
    {
        return (float) $this->getValue('amount');
    }

    /**
     * Get payment comment
     *
     * @return      string
     */
    public function getComment()
    {
        return $this->getValue('comment');
    }

    /**
     * Get merchant data
     *
     * @return      string
     */
    public function getMerchantData()
    {
        return $this->getValue('merchantdata');
    }

    /**
     * Set callback type
     *
     * @param       string      $type       Callback type
     */
    public function setType($type)
    {
        $this->offsetSet('type', $type);
    }
}