<?PHP
namespace PaynetEasy\Paynet\Data;

use \PaynetEasy\Paynet\Exception\PaynetException;

/**
 * Container for credit card data
 *
 */
class       CreditCard
extends     Data
implements  CreditCardInterface
{
    public function __construct($array)
    {
        $this->properties = array
        (
            'card_printed_name'         => true,
            'credit_card_number'        => true,
            'expire_month'              => true,
            'expire_year'               => true,
            'cvv2'                      => true
        );

        $this->validate_preg = array
        (
            'card_printed_name'         => '|^[\S\s]{1,128}$|i',
            'credit_card_number'        => '|^[0-9]{1,20}$|i',
            'expire_month'              => '|^[0-9]{1,2}$|i',
            'expire_year'               => '|^[0-9]{1,2}$|i',
            'cvv2'                      => '|^[0-9]{3,4}$|'
        );

        parent::__construct($array);
    }

    public function validate()
    {
        // Normalize value
        if(isset($this['credit_card_number']))
        {
            $this['credit_card_number']     = str_replace(array(' ','-','.',','), '', $this['credit_card_number']);
        }

        parent::validate();

        if($this['expire_month'] < 1 || $this['expire_month'] > 12)
        {
            $this->errors['expire_month'] = '%s is failed';

            throw new PaynetException(__METHOD__.' failed', $this->errors);
        }
    }
}