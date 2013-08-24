<?php

namespace PaynetEasy\PaynetEasyApi\Util;

use RuntimeException;

class PropertyAccessor
{
    /**
     * Get property value by property path.
     *
     * @param       object      $object             Object with data
     * @param       string      $propertyPath       Path to property
     * @param       boolean     $failOnError        Throw exception if error occurred or not
     *
     * @return      mixed|null                      Property value or null, if error occurred
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
            return static::getValue($firstObject, $pathRest, $failOnError);
        }
        elseif ($failOnError === true)
        {
            throw new RuntimeException("Object expected for property path '{$firstPropertyPath}', '" .
                                        gettype($firstObject) . "' given");
        }
    }

    /**
     * Set property value by property path.
     *
     * @param       object      $object             Object with data
     * @param       string      $propertyPath       Path to property
     * @param       mixed       $propertyValue      Value to set
     * @param       boolean     $failOnError        Throw exception if error occurred or not
     *
     * @return      mixed|null                      Setter return value or null, if error occurred
     *
     * @throws      RuntimeException                Property setter not found or parent property is not object
     */
    static public function setValue($object, $propertyPath, $propertyValue, $failOnError = true)
    {
        if (!is_object($object))
        {
            throw new RuntimeException('Object expected, ' . gettype($object) . ' given');
        }

        // immediately return value if propertyPath contains only one level
        if (strpos($propertyPath, '.') === false)
        {
            return static::setBySetter($object, $propertyPath, $propertyValue, $failOnError);
        }

        list($firstPropertyPath, $pathRest) = explode('.', $propertyPath, 2);

        $firstObject = static::getByGetter($object, $firstPropertyPath, $failOnError);

        // set value recursively while propertyPath has many levels
        if (is_object($firstObject))
        {
            return static::setValue($firstObject, $pathRest, $propertyValue, $failOnError);
        }
        elseif ($failOnError === true)
        {
            throw new RuntimeException("Object expected for property path '{$firstPropertyPath}', '" .
                                        gettype($firstObject) . "' given");
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
        elseif ($failOnUnknownGetter === true)
        {
            throw new RuntimeException("Unknown getter for property '{$propertyName}'");
        }
    }

    /**
     * Set property value by property name
     *
     * @param       object      $object                     Object with data
     * @param       string      $propertyName               Property name
     * @param       mixed       $propertyValue              Value to set
     * @param       boolean     $failOnUnknownGetter        Throw exception if setter not found or not
     *
     * @return      mixed|null                              Setter return value or null, if setter not found
     *
     * @throws      RuntimeException                        Property setter not found
     */
    static protected function setBySetter($object, $propertyName, $propertyValue, $failOnUnknownGetter)
    {
        $setter = array($object, 'set' . $propertyName);

        if (is_callable($setter))
        {
            return call_user_func($setter, $propertyValue);
        }
        elseif ($failOnUnknownGetter === true)
        {
            throw new RuntimeException("Unknown setter for property '{$propertyName}'");
        }
    }
}