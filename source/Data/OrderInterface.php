<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\Data;

interface OrderInterface
{
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
}