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
     * RecurrentCard referense ID
     *
     * @var integer
     */
    protected $cardReferenceId;

    /**
     * RecurrentCard CVV2
     *
     * @var integer
     */
    protected $cvv2;

    /**
     * Card holder name
     *
     * @var string
     */
    protected $cardPrintedName;

    /**
     * Card expiration year
     *
     * @var integer
     */
    protected $expireYear;

    /**
     * Card expiration month
     *
     * @var integer
     */
    protected $expireMonth;

    /**
     * Bank Identification Number
     *
     * @var integer
     */
    protected $bin;

    /**
     * The last four digits of PAN (card number)
     *
     * @var integer
     */
    protected $lastFourDigits;

    /**
     * {@inheritdoc}
     */
    public function setCardReferenceId($cardReferenceId)
    {
        $this->cardReferenceId = $cardReferenceId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCardReferenceId()
    {
        return $this->cardReferenceId;
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function setCardPrintedName($cardPrintedName)
    {
        $this->cardPrintedName = $cardPrintedName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCardPrintedName()
    {
        return $this->cardPrintedName;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpireYear($expireYear)
    {
        $this->expireYear = $expireYear;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireYear()
    {
        return $this->expireYear;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = $expireMonth;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireMonth()
    {
        return $this->expireMonth;
    }

    /**
     * {@inheritdoc}
     */
    public function setBin($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastFourDigits($lastFourDigits)
    {
        $this->lastFourDigits = $lastFourDigits;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastFourDigits()
    {
        return $this->lastFourDigits;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSetterByField($fieldName)
    {
        switch ($fieldName)
        {
            case 'id':
            case 'cardrefid':
            case 'card_ref_id':
            case 'card-ref-id':
            case 'source-card-ref-id':
            case 'destination-card-ref-id':
            {
                return 'setCardReferenceId';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }
}