<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Transport\Callback;
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
    public function getCallback(Callback $callback, array $callbackConfig = array())
    {
        $type = $callback->type();

        if (empty($type))
        {
            return new RedirectUrlCallback($callbackConfig);
        }

        if (in_array($type, static::$allowedServerCallbackUrlTypes))
        {
            $callbackProcessor = new ServerCallbackUrlCallback($callbackConfig);
            $callbackProcessor->setCallbackType($type);

            return $callbackProcessor;
        }

        throw new RuntimeException("Unknown callback type: {$type}");
    }
}