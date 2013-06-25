<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\OrderData;

use Exception;

interface OrderInterface
{
    /**
     * @todo Compare with Paynet Wiki
     * @todo Add comments
     */
    const STATE_NULL        = 'null';
    const STATE_INIT        = 'init';
    const STATE_REDIRECT    = 'redirect';
    const STATE_PROCESSING  = 'processing';
    const STATE_END         = 'end';

    /**
     * @todo Compare with Paynet Wiki
     * @todo Add comments
     */
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED   = 'approved';
    const STATUS_DECLINED   = 'declined';
    const STATUS_FILTERED   = 'filtered';
    const STATUS_ERROR      = 'error';

    /**
     * Set Merchant order identifier
     *
     * @param       string      $clientOrderId      Merchant order identifier
     *
     * @return      self
     */
    public function setClientOrderId($clientOrderId);

    /**
     * Get merchant order identifier
     *
     * @return      string
     */
    public function getClientOrderId();

    /**
     * Set unique identifier of transaction assigned by PaynetEasy
     *
     * @param       string      $paynetOrderId      Unique identifier of transaction assigned by PaynetEasy
     *
     * @return      self
     */
    public function setPaynetOrderId($paynetOrderId);

    /**
     * Get unique identifier of transaction assigned by PaynetEasy
     *
     * @return       string
     */
    public function getPaynetOrderId();

    /**
     * Set brief order description
     *
     * @param       string      $description        Brief order description
     *
     * @return      self
     */
    public function setDescription($description);

    /**
     * Get brief order description
     *
     * @return      string
     */
    public function getDescription();

    /**
     * Set destination to where the payment goes
     *
     * @param       string      $destination        Destination to where the payment goes
     */
    public function setDestination($destination);

    /**
     * Get destination to where the payment goes
     *
     * @return      string
     */
    public function getDestination();

    /**
     * Get amount to be charged
     *
     * @param       float       $amount             Amount to be charged
     *
     * @return      self
     */
    public function setAmount($amount);

    /**
     * Get amount to be charged
     *
     * @return      float
     */
    public function getAmount();

    /**
     * Get amount in cents (for control code generation)
     *
     * @return      integer
     */
    public function getAmountInCents();

    /**
     * Set currency the transaction is charged in (three-letter currency code)
     *
     * @param       string      $currency           Currency the transaction is charged in
     *
     * @return      self
     */
    public function setCurrency($currency);

    /**
     * Get currency the transaction is charged in (three-letter currency code)
     *
     * @return      string
     */
    public function getCurrency();

    /**
     * Set customer’s IP address
     *
     * @param       string      $ipAddress          Customer’s IP address
     *
     * @return      self
     */
    public function setIpAddress($ipAddress);

    /**
     * Get customer’s IP address
     *
     * @return      string
     */
    public function getIpAddress();

    /**
     * Set URL the original payment is made from
     *
     * @param       string      $siteUrl            URL the original payment is made from
     *
     * @return      self
     */
    public function setSiteUrl($siteUrl);

    /**
     * Get URL the original payment is made from
     *
     * @return      string
     */
    public function getSiteUrl();

    /**
     * Set order customer
     *
     * @param       \PaynetEasy\Paynet\OrderData\CustomerInterface        $customer       Order customer
     *
     * @return      self
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Get order customer
     *
     * @return      \PaynetEasy\Paynet\OrderData\CustomerInterface
     */
    public function getCustomer();

    /**
     * Set order credit card
     *
     * @param       \PaynetEasy\Paynet\OrderData\CreditCardInterface     $creditCard
     *
     * @return      self
     */
    public function setCreditCard(CreditCardInterface $creditCard);

    /**
     * Get credit card
     *
     * @return      \PaynetEasy\Paynet\OrderData\CreditCardInterface
     */
    public function getCreditCard();

    /**
     * Creates recurrent credit card for given id
     *
     * @param       string      $cardReferenceId      Recurrent credit card reference id
     *
     * @return      self
     */
    /**
     * @todo Move to another object
     */
    public function createRecurrentCardFrom($cardReferenceId);

    /**
     * Set order sorce recurrent card
     *
     * @param       \PaynetEasy\Paynet\OrderData\RecurrentCardInterface  $recurrentCard
     *
     * @return      self
     */
    public function setRecurrentCardFrom(RecurrentCardInterface $recurrentCard);

    /**
     * Get order source recurrent card
     *
     * @return      \PaynetEasy\Paynet\OrderData\RecurrentCardInterface
     */
    public function getRecurrentCardFrom();

    /**
     * Set order destination recurrent card
     *
     * @param       \PaynetEasy\Paynet\OrderData\RecurrentCardInterface  $recurrentCard
     *
     * @return      self
     */
    public function setRecurrentCardTo(RecurrentCardInterface $recurrentCard);

    /**
     * Get order destination recurrent card
     *
     * @return      \PaynetEasy\Paynet\OrderData\RecurrentCardInterface
     */
    public function getRecurrentCardTo();

    /**
     * Set order state
     *
     * @param       string      $state      Order state
     *
     * @return      self
     */
    /**
     * @todo More specific name and description needed
     */
    public function setState($state);

    /**
     * Get order state
     *
     * @return      string
     */
    /**
     * @todo More specific name and description needed
     */
    public function getState();

    /**
     * Set order status
     *
     * @param       string      $state      Order status
     *
     * @return      self
     */
    /**
     * @todo More specific name and description needed
     */
    public function setStatus($status);

    /**
     * Get order status
     *
     * @return      string
     */
    /**
     * @todo More specific name and description needed
     */
    public function getStatus();

    /**
     * Set order short comment
     *
     * @param       string      $comment    A short comment
     *
     * @return      self
     */
    public function setComment($comment);

    /**
     * Get order cancellation reason (up to 50 chars)
     *
     * @return      string                  Cancellation reason
     */
    public function getComment();

    /**
     * Adds new order error
     *
     * @param       Exception   $error      Order error
     *
     * @return      self
     */
    public function addError(Exception $error);

    /**
     * True if order has errors
     *
     * @return      boolean
     */
    public function hasErrors();

    /**
     * Get all order errors
     *
     * @return      array                   Order errors
     */
    public function getErrors();

    /**
     * Get order last message error
     *
     * @return      string
     */
    /**
     * @todo        Possibly not needed
     */
    public function getLastError();
}