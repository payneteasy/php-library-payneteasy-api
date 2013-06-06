<?PHP
namespace PaynetEasy\Paynet\Data;

/**
 * Container for Reccurent Credit Card data
 *
 */
class RecurrentCard    extends     Data
{
    public function __construct($cardrefid)
    {
        $this->properties = array
        (
            'cardrefid'                 => true,
        );

        $this->validate_preg = array
        (
            'cardrefid'                 => '|^[0-9]{1,20}$|i'
        );

        parent::__construct(array('cardrefid' => $cardrefid));
    }

    public function cardRefId()
    {
        if(!$this->offsetExists('cardrefid'))
        {
            return '';
        }
        else
        {
            return $this->offsetGet('cardrefid');
        }
    }
}