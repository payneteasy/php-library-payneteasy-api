<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

interface CallbackFactoryInterface
{
    /**
     * Get callback processor by callback data
     *
     * @param       \PaynetEasy\PaynetEasyApi\Transport\CallbackResponse       $callback               Callback data
     * @param       array                                       $callbackConfig         Callback processor config
     *
     * @return      \PaynetEasy\PaynetEasyApi\Callback\CallbackInterface                       Callback processor
     */
    public function getCallback(CallbackResponse $callback, array $callbackConfig = array());
}