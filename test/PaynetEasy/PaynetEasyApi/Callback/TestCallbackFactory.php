<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

/**
 * Callback factory, that also allows test callback types
 */
class TestCallbackFactory extends CallbackFactory
{
    static protected $allowedCallbackTypes = array
    (
        'customer_return',
        'fake',
        'notcallback'
    );
}
