<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use RuntimeException;

class CallbackFactory implements CallbackFactoryInterface
{
    /**
     * Allowed types for ServerCallbackUrlCallback
     *
     * @var array
     */
    static protected $allowedServerCallbackUrlTypes = array
    (
        'sale',
        'reversal',
        'chargeback'
    );

    /**
     * {@inheritdoc}
     */
    public function getCallback(CallbackResponse $callback)
    {
        $callbackType   = $callback->getType();

        if (empty($callbackType))
        {
            return new RedirectUrlCallback('redirect_url');
        }

        $callbackClass  = __NAMESPACE__ . '\\' . ucfirst($callbackType) . 'Callback';

        if (class_exists($callbackClass, true))
        {
            return new $callbackClass($callbackType);
        }

        if (in_array($callbackType, static::$allowedServerCallbackUrlTypes))
        {
            return new ServerCallbackUrlCallback($callbackType);
        }

        throw new RuntimeException("Unknown callback class '{$callbackClass}' for callback with type '{$callbackType}'");
    }
}