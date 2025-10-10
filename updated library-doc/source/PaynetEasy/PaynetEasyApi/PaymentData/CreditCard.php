<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

/**
 * Container for credit card data
 *
 */
class CreditCard extends Data
{
    /**
     * CreditCard CVV2
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
     * Set RecurrentCard CVV2
     *
     * @param       integer     $cvv2                   RecurrentCard CVV2
     */
    public function setCvv2($cvv2)
    {
        $this->cvv2 = $cvv2;

        return $this;
    }

    /**
     * Get card CVV2 code
     *
     * @return  integer
     */
    public function getCvv2()
    {
        return $this->cvv2;
    }

    /**
     * Set card holder name
     *
     * @param       string      $cardPrintedName        Card holder name
     *
     * @return      self
     */
    public function setCardPrintedName($cardPrintedName)
    {
        $this->cardPrintedName = $cardPrintedName;

        return $this;
    }

    /**
     * Get card holder name
     *
     * @return      string
     */
    public function getCardPrintedName()
    {
        return $this->cardPrintedName;
    }

    /**
     * Set credit card number
     *
     * @param       integer     $creditCardNumber           Credit card number
     *
     * @return      self
     */
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = str_replace(array(' ', '-', '_', '.', ','), '', $creditCardNumber);

        return $this;
    }

    /**
     * Get credit card number
     *
     * @return      integer
     */
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
    }

    /**
     * Set card expiration year
     *
     * @param       integer     $expireYear             Card expiration year
     *
     * @return      self
     */
    public function setExpireYear($expireYear)
    {
        $this->expireYear = $expireYear;

        return $this;
    }

    /**
     * Get card expiration year
     *
     * @return      integer
     */
    public function getExpireYear()
    {
        return $this->expireYear;
    }

    /**
     * Set card expiration month
     *
     * @param       integer     $expireMonth            Card expiration month
     *
     * @return      self
     */
    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = $expireMonth;

        return $this;
    }

    /**
     * Get card expiration month
     *
     * @return      self
     */
    public function getExpireMonth()
    {
        return $this->expireMonth;
    }
}