<?php
namespace PaynetEasy\Paynet\Transport;

use PaynetEasy\Paynet\Transport\Response;

/**
 * Merchant Callback
 * Sale, Return, Chargeback callback simple URL
 *
 * see: http://wiki.payneteasy.com/index.php/PnE:Merchant_Callback#Sale.2C_Return_Callback_Parameters
 */
class CallbackResponse extends Response
{
    public function amount()
    {
        return (float) $this->getValue('amount');
    }

    public function comment()
    {
        return $this->getValue('comment');
    }

    public function merchantData()
    {
        return $this->getValue('merchantdata');
    }
}