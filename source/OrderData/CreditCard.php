<?php
namespace PaynetEasy\Paynet\OrderData;

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
        $this->cvv2 = $cvv2;

        return $this;
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
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = str_replace(array(' ','-','.',','), '', $creditCardNumber);

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
}