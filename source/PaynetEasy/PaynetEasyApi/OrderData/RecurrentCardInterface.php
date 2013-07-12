<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\PaynetEasyApi\OrderData;

interface RecurrentCardInterface
{
    /**
     * Set RecurrentCard referense ID
     *
     * @param       integer     $cardReferenceId        RecurrentCard referense ID
     *
     * @return      self
     */
    public function setCardReferenceId($cardReferenceId);

    /**
     * Get RecurrentCard referense ID
     *
     * @return  integer
     */
    public function getCardReferenceId();

    /**
     * Set RecurrentCard CVV2
     *
     * @param       integer     $cvv2                   RecurrentCard CVV2
     */
    public function setCvv2($cvv2);

    /**
     * Get card CVV2 code
     *
     * @return  integer
     */
    public function getCvv2();

    /**
     * Set card holder name
     *
     * @param       string      $cardPrintedName        Card holder name
     *
     * @return      self
     */
    public function setCardPrintedName($cardPrintedName);

    /**
     * Get card holder name
     *
     * @return      string
     */
    public function getCardPrintedName();

    /**
     * Set card expiration year
     *
     * @param       integer     $expireYear             Card expiration year
     *
     * @return      self
     */
    public function setExpireYear($expireYear);

    /**
     * Get card expiration year
     *
     * @return      integer
     */
    public function getExpireYear();

    /**
     * Set card expiration month
     *
     * @param       integer     $expireMonth            Card expiration month
     *
     * @return      self
     */
    public function setExpireMonth($expireMonth);

    /**
     * Get card expiration month
     *
     * @return      self
     */
    public function getExpireMonth();

    /**
     * Set Bank Identification Number
     *
     * @param       integer     $bin                    Bank Identification Number
     *
     * @return      self
     */
    public function setBin($bin);

    /**
     * Get Bank Identification Number
     *
     * @return      integer
     */
    public function getBin();

    /**
     * Set last four digits of PAN (card number)
     *
     * @param       integer     $lastFourDigits         The last four digits of PAN (card number)
     *
     * @return      self
     */
    public function setLastFourDigits($lastFourDigits);

    /**
     * Get last four digits of PAN (card number)
     *
     * @return      integer
     */
    public function getLastFourDigits();
}