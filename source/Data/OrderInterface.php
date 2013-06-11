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