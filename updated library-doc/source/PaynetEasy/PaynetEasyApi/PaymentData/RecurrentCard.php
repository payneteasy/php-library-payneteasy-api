<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

/**
 * Container for Reccurent Credit Card data
 *
 */
class RecurrentCard extends Data
{
    /**
     * RecurrentCard reference ID
     *
     * @var integer
     */
    protected $paynetId;

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
     * Unique card identifier to use for loyalty programs or fraud checks.
     *
     * @var string
     */
    protected $cardHashId;

    /**
     * Type of customer credit card (VISA,MASTERCARD, etc).
     *
     * @var string
     */
    protected $cardType;

    /**
     * Set RecurrentCard referense ID
     *
     * @param       integer     $paynetId       RecurrentCard referense ID
     *
     * @return      self
     */
    public function setPaynetId($paynetId)
    {
        $this->paynetId = $paynetId;

        return $this;
    }

    /**
     * Get RecurrentCard referense ID
     *
     * @return  integer
     */
    public function getPaynetId()
    {
        return $this->paynetId;
    }

    /**
     * Set RecurrentCard CVV2
     *
     * @param       integer     $cvv2                   RecurrentCard CVV2
     */
    public function setCvv2($cvv2)
    {
        $this->cvv2 = $cvv2;
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

    /**
     * Set Bank Identification Number
     *
     * @param       integer     $bin                    Bank Identification Number
     *
     * @return      self
     */
    public function setBin($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Get Bank Identification Number
     *
     * @return      integer
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * Set last four digits of PAN (card number)
     *
     * @param       integer     $lastFourDigits         The last four digits of PAN (card number)
     *
     * @return      self
     */
    public function setLastFourDigits($lastFourDigits)
    {
        $this->lastFourDigits = $lastFourDigits;

        return $this;
    }

    /**
     * Get last four digits of PAN (card number)
     *
     * @return      integer
     */
    public function getLastFourDigits()
    {
        return $this->lastFourDigits;
    }

    /**
     * Set unique card identifier to use for loyalty programs or fraud checks.
     *
     * @param       string      $cardHashId     Unique card identifier to use for loyalty programs or fraud checks.
     */
    function setCardHashId($cardHashId) {
        $this->cardHashId = $cardHashId;
    }

    /**
     * Get unique card identifier to use for loyalty programs or fraud checks.
     *
     * @return      string      Unique card identifier to use for loyalty programs or fraud checks.
     */
    function getCardHashId() {
        return $this->cardHashId;
    }

    /**
     * Set type of customer credit card (VISA,MASTERCARD, etc).
     *
     * @param       string      $cardType       Type of customer credit card (VISA,MASTERCARD, etc).
     */
    function setCardType($cardType) {
        $this->cardType = $cardType;
    }

    /**
     * Get type of customer credit card (VISA,MASTERCARD, etc).
     *
     * @return      string      Type of customer credit card (VISA,MASTERCARD, etc).
     */
    function getCardType() {
        return $this->cardType;
    }

}