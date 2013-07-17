<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use Exception;

interface PaymentInterface
{
    /**
     * Payment created in bank
     */
    const STAGE_CREATED     = 'created';

    /**
     * Customer is redirected to Paynet to perform additional steps
     */
    const STAGE_REDIRECTED  = 'redirected';

    /**
     * Payment processing is ended
     */
    const STAGE_FINISHED    = 'ended';

    /**
     * Payment is now processing
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * Payment approved
     */
    const STATUS_APPROVED   = 'approved';

    /**
     * Payment declined by bank
     */
    const STATUS_DECLINED   = 'declined';

    /**
     * Payment declined by Paynet filters
     */
    const STATUS_FILTERED   = 'filtered';

    /**
     * Payment processed with error
     */
    const STATUS_ERROR      = 'error';

    /**
     * Set Merchant payment identifier
     *
     * @param       string      $clientPaymentId        Merchant payment identifier
     *
     * @return      self
     */
    public function setClientPaymentId($clientPaymentId);

    /**
     * Get merchant payment identifier
     *
     * @return      string
     */
    public function getClientPaymentId();

    /**
     * Set unique identifier of transaction assigned by PaynetEasy
     *
     * @param       string      $paynetPaymentId        Unique identifier of transaction assigned by PaynetEasy
     *
     * @return      self
     */
    public function setPaynetPaymentId($paynetPaymentId);

    /**
     * Get unique identifier of transaction assigned by PaynetEasy
     *
     * @return       string
     */
    public function getPaynetPaymentId();

    /**
     * Set brief payment description
     *
     * @param       string      $description        Brief payment description
     *
     * @return      self
     */
    public function setDescription($description);

    /**
     * Get brief payment description
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
     * Set payment customer
     *
     * @param       CustomerInterface       $customer           Payment customer
     *
     * @return      self
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Get payment customer
     *
     * @return      CustomerInterface
     */
    public function getCustomer();

    /**
     * Set payment credit card
     *
     * @param       CreditCardInterface     $creditCard         Payment credit card
     *
     * @return      self
     */
    public function setCreditCard(CreditCardInterface $creditCard);

    /**
     * Get credit card
     *
     * @return      CreditCardInterface
     */
    public function getCreditCard();

    /**
     * Set payment sorce recurrent card
     *
     * @param       RecurrentCardInterface      $recurrentCard      Source recurrent card
     *
     * @return      self
     */
    public function setRecurrentCardFrom(RecurrentCardInterface $recurrentCard);

    /**
     * Get payment source recurrent card
     *
     * @return      RecurrentCardInterface
     */
    public function getRecurrentCardFrom();

    /**
     * Set payment destination recurrent card
     *
     * @param       RecurrentCardInterface      $recurrentCard      Destination recurrent card
     *
     * @return      self
     */
    public function setRecurrentCardTo(RecurrentCardInterface $recurrentCard);

    /**
     * Get payment destination recurrent card
     *
     * @return      RecurrentCardInterface
     */
    public function getRecurrentCardTo();

    /**
     * Set payment processing stage
     *
     * @param       string      $processingStage      Payment transport stage
     *
     * @return      self
     */
    public function setProcessingStage($processingStage);

    /**
     * Get payment processing state
     *
     * @return      string
     */
    public function getProcessingStage();

    /**
     * True, if payment created in bank
     *
     * @return      boolean
     */
    public function isCreated();

    /**
     * True, if customer is redirected to Paynet to perform additional steps
     *
     * @return      boolean
     */
    public function isRedirected();

    /**
     * True, if transport stage is finished
     *
     * @return      boolean
     */
    public function isFinished();

    /**
     * Set payment bank status
     *
     * @param       string      $status     Payment bank status
     *
     * @return      self
     */
    public function setStatus($status);

    /**
     * Get payment bank status
     *
     * @return      string
     */
    public function getStatus();

    /**
     * True, if payment is now processing
     *
     * @return      boolean
     */
    public function isProcessing();

    /**
     * True, if payment approved
     *
     * @return      boolean
     */
    public function isApproved();

    /**
     * True, if payment declined
     *
     * @return      boolean
     */
    public function isDeclined();

    /**
     * True, if error occured when processing payment
     *
     * @return      boolean
     */
    public function isError();

    /**
     * Set payment short comment
     *
     * @param       string      $comment    A short comment
     *
     * @return      self
     */
    public function setComment($comment);

    /**
     * Get payment cancellation reason (up to 50 chars)
     *
     * @return      string      Cancellation reason
     */
    public function getComment();

    /**
     * Adds new payment error
     *
     * @param       Exception       $error      Payment error
     *
     * @return      self
     */
    public function addError(Exception $error);

    /**
     * True if payment has errors
     *
     * @return      boolean
     */
    public function hasErrors();

    /**
     * Get all payment errors
     *
     * @return      array       Payment errors
     */
    public function getErrors();

    /**
     * Get payment last error
     *
     * @return      Exception
     */
    public function getLastError();
}