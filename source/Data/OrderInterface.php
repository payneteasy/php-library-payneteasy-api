<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\Data;

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
    const STATE_WAIT        = 'wait';
    const STATE_END         = 'end';

    /**
     * @todo Compare with Paynet Wiki
     * @todo Add comments
     */
    const STATUS_APPROVED   = 'approved';
    const STATUS_DECLINED   = 'declined';
    const STATUS_ERROR      = 'error';

    /**
     * Set order customer
     *
     * @param       \PaynetEasy\Paynet\Data\CustomerInterface        $customer       Order customer
     *
     * @return      self
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Get order customer
     *
     * @return      \PaynetEasy\Paynet\Data\CustomerInterface
     */
    public function getCustomer();

    /**
     * True if order has customer
     *
     * @return      boolean
     */
    public function hasCustomer();

    /**
     * Set order credit card
     *
     * @param       \PaynetEasy\Paynet\Data\CreditCardInterface     $creditCard
     *
     * @return      self
     */
    public function setCreditCard(CreditCardInterface $creditCard);

    /**
     * Get credit card
     *
     * @return      \PaynetEasy\Paynet\Data\CreditCardInterface
     */
    public function getCreditCard();

    /**
     * True if order has credit card
     *
     * @return      boolean
     */
    public function hasCreditCard();

    /**
     * Creates recurrent credit card for given id
     *
     * @param       string      $cardRefId      Recurrent credit card reference id
     *
     * @return      self
     */
    public function createRecurrentCard($cardRefId);

    /**
     * Set order recurrent card
     *
     * @param       \PaynetEasy\Paynet\Data\RecurrentCardInterface  $recurrentCard
     *
     * @return      self
     */
    public function setRecurrentCard(RecurrentCardInterface $recurrentCard);

    /**
     * Get order recurrent card
     *
     * @return      \PaynetEasy\Paynet\Data\RecurrentCardInterface
     */
    public function getRecurrentCard();

    /**
     * True if order has recurrent card
     *
     * @return      boolean
     */
    public function hasRecurrentCard();

    /**
     * Get all allowed order states
     *
     * @return      array
     */
    /**
     * @todo More specific name and description needed
     */
    static public function getAllowedStates();

    /**
     * Get all allowed order statuses
     *
     * @return      array
     */
    /**
     * @todo More specific name and description needed
     */
    static public function getAllowedStatuses();

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
     * Set order cancellation reason (up to 50 chars)
     *
     * @param       string      cancelReason        Cancellation reason
     *
     * @return      self
     */
    public function setCancelReason($cancelReason);

    /**
     * Get order cancellation reason (up to 50 chars)
     *
     * @return      string                          Cancellation reason
     */
    public function getCancelReason();

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