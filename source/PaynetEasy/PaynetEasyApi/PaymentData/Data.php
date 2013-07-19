<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use PaynetEasy\PaynetEasyApi\Utils\String;
use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;

use Serializable;
use RuntimeException;

class Data implements Serializable
{
    /**
     * Initialize object from input array.
     * For each array field will be called property setter.
     * If the setter is not found, and $failOnUnknownField = true,
     * exception will be trown.
     *
     * :NOTICE:         Imenem          20.06.13
     *
     * Every child class can have own logic
     * to find setter by array field name
     *
     * @param       array       $array                  Input array with object data
     * @param       boolean     $failOnUnknownField     If true, exception will be trown if the setter is not found
     *
     * @throws      RuntimeException                    Setter is not found
     */
    public function __construct($array = array(), $failOnUnknownField = true)
    {
        foreach ($array as $fieldName => $fieldValue)
        {
            $propertyName = $this->getPropertyByField($fieldName);
            PropertyAccessor::setValue($this, $propertyName, $fieldValue, $failOnUnknownField);
        }
    }

    /**
     * Get property setter name by input array field name
     *
     * @param       string      $fieldName          Input array field name
     *
     * @return      string                          Property setter name
     */
    protected function getPropertyByField($fieldName)
    {
        return String::camelize($fieldName);
    }

    /**
     * Serialize all object scalar properties to string
     *
     * @return      string
     */
    public function serialize()
    {
        $objectData = array();

        foreach ($this as $propertyName => $propertyValue)
        {
            if (is_scalar($propertyValue))
            {
                $objectData[$propertyName] = $propertyValue;
            }
        }

        return serialize($objectData);
    }

    /**
     * Unserialize object from string
     *
     * @param       string      $serialized     Object data
     */
    public function unserialize($serialized)
    {
        $objectData = unserialize($serialized);

        foreach ($objectData as $propertyName => $propertyValue)
        {
            $this->{$propertyName} = $propertyValue;
        }
    }
}