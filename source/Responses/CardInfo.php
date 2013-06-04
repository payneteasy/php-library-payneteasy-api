<?PHP
namespace PaynetEasy\Paynet\Responses;

/**
 * Card Information Response
 *
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Information_request_parameters
 */
class CardInfo      extends Response
{
    public function cardPrintedName()
    {
        return $this->get_value('card-printed-name');
    }

    public function expireYear()
    {
        return $this->get_value('expire-year');
    }

    public function expireMonth()
    {
        return $this->get_value('expire-month');
    }

    public function bin()
    {
        return $this->get_value('bin');
    }

    public function lastFourDigits()
    {
        return $this->get_value('last-four-digits');
    }

    public function serialNumber()
    {
        return $this->get_value('serial-number');
    }
}