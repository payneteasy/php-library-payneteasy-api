<?php

namespace PaynetEasy\Paynet\Utils;

use RuntimeException;

class PropertyAccessor
{
    /**
     * Get property value by property path.
     *
     * @param       object      $object             Object with data
     * @param       string      $propertyPath       Path to property
     * @param       boolean     $failOnError        Throw exception if error occured or not
     *
     * @return      mixed|null                      Property value or null, if error ocured
     *
     * @throws      RuntimeException                Property getter not found or parent property is not object
     */
    static public function getValue($object, $propertyPath, $failOnError = true)
    {
        if (!is_object($object))
        {
            throw new RuntimeException('Object expected, ' . gettype($object) . ' given');
        }

        // immediately return value if propertyPath contains only one level
        if (strpos($propertyPath, '.') === false)
        {
            return static::getByGetter($object, $propertyPath, $failOnError);
        }

        list($firstPropertyPath, $pathRest) = explode('.', $propertyPath, 2);

        $firstObject = static::getByGetter($object, $firstPropertyPath, $failOnError);

        // get value recursively while propertyPath has many levels
        if (is_object($firstObject))
        {
            return static::getValue($firstObject, $pathRest);
        }

        if ($failOnError === true)
        {
            throw new RuntimeException("Object expected for property path {$firstPropertyPath}, " .
                                        gettype($firstObject) . ' given');
        }
    }

    /**
     * Get property value by property name
     *
     * @param       object      $object                     Object with data
     * @param       string      $propertyName               Property name
     * @param       boolean     $failOnUnknownGetter        Throw exception if getter not found or not
     *
     * @return      mixed|null                              Property value or null, if getter not found
     *
     * @throws      RuntimeException                        Property getter not found
     */
    static protected function getByGetter($object, $propertyName, $failOnUnknownGetter)
    {
        $getter = array($object, 'get' . $propertyName);

        if (is_callable($getter))
        {
            return call_user_func($getter);
        }

        if ($failOnUnknownGetter === true)
        {
            throw new RuntimeException("Unknown getter for property '{$propertyName}'");
        }
    }
}