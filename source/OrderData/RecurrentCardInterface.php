<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\Paynet\OrderData;

/**
 * @todo Implement interface
 */
interface RecurrentCardInterface
{
    /**
     * Get card reference id
     *
     * @return  string
     */
    public function getCardRefId();

    /**
     * Get card CVV2 code
     *
     * @return  integer
     */
    public function getCvv2();
}