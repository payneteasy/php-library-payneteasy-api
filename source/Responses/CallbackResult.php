<?PHP
namespace PaynetEasy\Paynet\Responses;

use \PaynetEasy\Paynet\Exceptions\CallbackException;

/**
 * Merchant Callbacks
 * Sale, Return, Chargeback callback simple URL
 *
 * see: http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks#Sale.2C_Return_Callback_Parameters
 */
class CallbackResult extends Response
{
    const SALE          = 'sale';
    const REVERSAL      = 'reversal';
    const CHARGEBACK    = 'chargeback';

    public function type()
    {
        $result         = strtolower($this->getValue('type'));

        switch($result)
        {
            case self::SALE:
            case self::REVERSAL:
            case self::CHARGEBACK:
            {
                return $result;
            }

            default:
            {
                throw new CallbackException("invalid transaction type: '$result'");
            }
        }
    }

    public function amount()
    {
        return (float)$this->getValue('amount');
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