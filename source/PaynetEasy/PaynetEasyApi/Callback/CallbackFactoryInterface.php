<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

interface CallbackFactoryInterface
{
    /**
     * Get callback processor by callback type
     *
     * @param       string      $callback       Callback type
     *
     * @return      CallbackInterface           Callback processor
     */
    public function getCallback($callbackType);
}