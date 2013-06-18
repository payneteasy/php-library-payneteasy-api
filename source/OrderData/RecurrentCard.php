<?PHP
namespace PaynetEasy\Paynet\OrderData;

/**
 * Container for Reccurent Credit Card data
 *
 */
class       RecurrentCard
extends     Data
implements  RecurrentCardInterface
{
    /**
     * RecurrentCard CVV2
     *
     * @var integer
     */
    protected $cvv2;

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

    /**
     * {@inheritdoc}
     */
    public function getCardRefId()
    {
        if($this->offsetExists('cardrefid'))
        {
            return $this->offsetGet('cardrefid');
        }
    }

    /**
     * Set RecurrentCard CVV2
     * @param type $cvv2
     */
    public function setCvv2($cvv2)
    {
        $this->cvv2 = $cvv2;
    }

    /**
     * {@inheritdoc}
     */
    public function getCvv2()
    {
        return $this->cvv2;
    }
}