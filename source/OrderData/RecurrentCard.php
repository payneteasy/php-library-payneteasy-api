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
        $this->validateValue($cardReferenceId, '#^[0-9]{1,20}$#i');

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
        $this->validateValue($cvv2, '#^[0-9]{3,4}$#i');

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
        $this->validateValue($cardPrintedName, '#^[\S\s]{1,128}$#i');

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
        $this->validateValue($expireYear, '#^[0-9]{1,2}$#i');

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
        $this->validateValue($expireMonth, '#^[0-9]{1,2}$#i');

        if($expireMonth < 1 || $expireMonth > 12)
        {
            throw new ValidationException("Expire month must be beetween 1 and 12, " .
                                          "'{$expireMonth}' given");
        }

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
        $this->validateValue($bin, '#^[0-9]{4,6}$#i');

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
        $this->validateValue($lastFourDigits, '#^[0-9]{4}$#i');

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
            {
                return 'setCardReferenceId';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldByProperty($propertyName)
    {
        switch ($propertyName)
        {
            case 'cardReferenceId':
            {
                return 'cardrefid';
            }
            default:
            {
                return parent::getFieldByProperty($propertyName);
            }
        }
    }
}