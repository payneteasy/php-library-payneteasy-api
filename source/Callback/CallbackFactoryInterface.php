<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Transport\CallbackResponse;

interface CallbackFactoryInterface
{
    /**
     * Get callback processor by callback data
     *
     * @param       \PaynetEasy\Paynet\Transport\CallbackResponse       $callback               Callback data
     * @param       array                                       $callbackConfig         Callback processor config
     *
     * @return      \PaynetEasy\Paynet\Callback\CallbackInterface                       Callback processor
     */
    public function getCallback(CallbackResponse $callback, array $callbackConfig = array());
}