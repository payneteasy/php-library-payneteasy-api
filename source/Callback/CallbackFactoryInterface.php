<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Transport\Callback;

interface CallbackFactoryInterface
{
    /**
     * Get callback processor by callback data
     *
     * @param       \PaynetEasy\Paynet\Transport\Callback       $callback               Callback data
     * @param       array                                       $callbackConfig         Callback processor config
     *
     * @return      \PaynetEasy\Paynet\Callback\CallbackInterface                       Callback processor
     */
    public function getCallback(Callback $callback, array $callbackConfig = array());
}