<?PHP
namespace PaynetEasy\Paynet\Responses;

use PaynetEasy\Paynet\Transport\Response;

/**
 * Card Information Response
 *
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 */
class CardInfo extends Response
{
    public function cardPrintedName()
    {
        return $this->getValue('card-printed-name');
    }

    public function expireYear()
    {
        return $this->getValue('expire-year');
    }

    public function expireMonth()
    {
        return $this->getValue('expire-month');
    }

    public function bin()
    {
        return $this->getValue('bin');
    }

    public function lastFourDigits()
    {
        return $this->getValue('last-four-digits');
    }

    public function serialNumber()
    {
        return $this->getValue('serial-number');
    }
}