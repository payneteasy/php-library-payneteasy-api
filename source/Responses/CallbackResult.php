<?PHP
namespace PaynetEasy\Paynet\Responses;

use \PaynetEasy\Paynet\Exceptions\CallbackResultException;

/**
 * Merchant Callbacks
 * Sale, Return, Chargeback callback simple URL
 *
 * see: http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks#Sale.2C_Return_Callback_Parameters
 */
class CallbackResult      extends Response
{
    const SALE          = 'sale';
    const REVERSAL      = 'reversal';
    const CHARGEBACK    = 'chargeback';

    public function type()
    {
        $result         = strtolower($this->get_value('type'));

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
                throw new CallbackResultException("invalid transaction type: '$result'");
            }
        }
    }

    public function amount()
    {
        return (float)$this->get_value('amount');
    }

    public function comment()
    {
        return $this->get_value('comment');
    }

    public function merchantdata()
    {
        return $this->get_value('merchantdata');
    }
}