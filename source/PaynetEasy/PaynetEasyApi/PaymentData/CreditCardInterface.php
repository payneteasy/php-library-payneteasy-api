<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\PaynetEasyApi\PaymentData;

interface CreditCardInterface
{
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
     * Set credit card number
     *
     * @param       integer     $creditCardNumber           Credit card number
     *
     * @return      self
     */
    public function setCreditCardNumber($creditCardNumber);

    /**
     * Get credit card number
     *
     * @return      integer
     */
    public function getCreditCardNumber();
    
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
}