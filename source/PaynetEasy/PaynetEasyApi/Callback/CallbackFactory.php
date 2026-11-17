<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Util\StringHelper;
use RuntimeException;

class CallbackFactory implements CallbackFactoryInterface
{
    /**
     * Allowed types for ServerCallbackUrlCallback
     *
     * @var array
     */
    static protected $allowedPaynetEasyCallbackTypes = array
    (
        'sale',
        'reversal',
        'chargeback'
    );

    /**
     * Callback types, that have own callback class (<CamelizedType>Callback)
     *
     * @var array
     */
    static protected $allowedCallbackTypes = array
    (
        'customer_return'
    );

    /**
     * Interface, that callback class must implement
     *
     * @var     string
     */
    static protected $callbackInterface = 'PaynetEasy\PaynetEasyApi\Callback\CallbackInterface';

    /**
     * {@inheritdoc}
     */
    public function getCallback($callbackType)
    {
        $callbackClass  = __NAMESPACE__ . '\\' . StringHelper::camelize($callbackType) . 'Callback';

        // Callback type comes from untrusted request data:
        // never autoload a class for a type that is not explicitly allowed
        if (    in_array($callbackType, static::$allowedCallbackTypes, true)
            &&  class_exists($callbackClass, true))
        {
            return $this->instantiateCallback($callbackClass, $callbackType);
        }

        if (in_array($callbackType, static::$allowedPaynetEasyCallbackTypes, true))
        {
            return $this->instantiateCallback(__NAMESPACE__ . '\\PaynetEasyCallback', $callbackType);
        }

        throw new RuntimeException("Unknown callback class '{$callbackClass}' for callback with type '{$callbackType}'");
    }

    /**
     * Method check callback class and return new callback object
     *
     * @param       string      $callbackClass      Callback class
     * @param       string      $callbackType       Callback type
     *
     * @return      CallbackInterface               New callback object
     *
     * @throws      RuntimeException                Callback does not implements CallbackInterface
     */
    protected function instantiateCallback($callbackClass, $callbackType)
    {
        if (!is_a($callbackClass, static::$callbackInterface, true))
        {
            throw new RuntimeException("Callback class '{$callbackClass}' does not implements '" .
                                       static::$callbackInterface . "' interface.");
        }

        return new $callbackClass($callbackType);
    }
}