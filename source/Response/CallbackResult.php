<?PHP
namespace PaynetEasy\Paynet\Response;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exception\CallbackException;

/**
 * Merchant Callback
 * Sale, Return, Chargeback callback simple URL
 *
 * see: http://wiki.payneteasy.com/index.php/PnE:Merchant_Callback#Sale.2C_Return_Callback_Parameters
 */
class CallbackResult extends Response
{
    const SALE          = 'sale';
    const REVERSAL      = 'reversal';
    const CHARGEBACK    = 'chargeback';

    public function type()
    {
        $type = parent::type();

        if (!in_array($type, array(self::SALE, self::REVERSAL, self::CHARGEBACK)))
        {
            throw new CallbackException("Invalid callback result type: '{$type}'");
        }

        return $type;
    }

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