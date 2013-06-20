<?PHP
namespace PaynetEasy\Paynet\OrderData;

use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * Container for credit card data
 *
 */
class       CreditCard
extends     Data
implements  CreditCardInterface
{
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
     * Credit card number
     *
     * @var integer
     */
    protected $creditCardNumber;

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
    public function setCreditCardNumber($creditCardNumber)
    {
        $cleanNumber = str_replace(array(' ','-','.',','), '', $creditCardNumber);

        $this->validateValue($cleanNumber, '#^[0-9]{1,20}$#i');

        $this->creditCardNumber = $cleanNumber;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
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
}