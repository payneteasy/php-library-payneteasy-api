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
    protected $cardRefId;

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
     * @param       integer     $cardRefId          RecurrentCard referense ID
     */
    public function __construct($cardRefId = null)
    {
        if (!empty($cardRefId))
        {
            $this->setCardRefId($cardRefId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCardRefId($cardRefId)
    {
        $this->validateValue($cardRefId, '#^[0-9]{1,20}$#i');

        $this->cardRefId = $cardRefId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCardRefId()
    {
        return $this->cardRefId;
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
}