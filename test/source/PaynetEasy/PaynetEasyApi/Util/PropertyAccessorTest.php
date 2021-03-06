<?php

namespace PaynetEasy\PaynetEasyApi\Util;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-20 at 14:37:44.
 */
class PropertyAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testGetValueProvider
     */
    public function testGetValue($propertyPath, $expectedValue)
    {
        $actualValue = PropertyAccessor::getValue(new TestObject, $propertyPath, false);

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function testGetValueProvider()
    {
        return array(
        array
        (
            'testProperty',
            'test'
        ),
        array
        (
            'testObject.testProperty',
            'test'
        ),
        array
        (
            'testProperty.testProperty',
            null
        ),
        array
        (
            'unknownProperty',
            null
        ));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Object expected, array given
     */
    public function testGetValueWithNoObject()
    {
        PropertyAccessor::getValue(array('test' => 1), 'test');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Object expected for property path 'testProperty', 'string' given
     */
    public function testGetValueWithNoObjectInProperty()
    {
        PropertyAccessor::getValue(new TestObject, 'testProperty.testProperty');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown getter for property 'test'
     */
    public function testGetValueWithNoGetter()
    {
        PropertyAccessor::getValue(new TestObject, 'test');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Object expected, array given
     */
    public function testSetValueWithNoObject()
    {
        PropertyAccessor::setValue(array('test' => 1), 'test', 'new value');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Object expected for property path 'testProperty', 'string' given
     */
    public function testSetValueWithNoObjectInProperty()
    {
        PropertyAccessor::setValue(new TestObject, 'testProperty.testProperty', 'new value');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown setter for property 'test'
     */
    public function testSetValueWithNoGetter()
    {
        PropertyAccessor::setValue(new TestObject, 'test', 'new value');
    }

    /**
     * @dataProvider testSetValueProvider
     */
    public function testSetValue($propertyPath, $expectedValue, $failOnError = true)
    {
        $testObject = new TestObject;

        PropertyAccessor::setValue($testObject, $propertyPath, $expectedValue, $failOnError);

        $this->assertEquals($expectedValue, PropertyAccessor::getValue($testObject, $propertyPath, $failOnError));
    }

    public function testSetValueProvider()
    {
        return array(
        array
        (
            'testProperty',
            'test'
        ),
        array
        (
            'testObject.testProperty',
            'test'
        ),
        array
        (
            'testProperty.testProperty',
            null,
            false
        ),
        array
        (
            'unknownProperty',
            null,
            false
        ));
    }
}

class TestObject
{
    public $testProperty = 'test';

    public $testObject;

    public $emptyProperty;

    public function getTestProperty()
    {
        return $this->testProperty;
    }

    public function getTestObject()
    {
        if (!is_object($this->testObject))
        {
            $this->testObject = new static;
        }

        return $this->testObject;
    }

    public function getEmptyProperty()
    {
        return $this->emptyProperty;
    }

    public function setTestProperty($value)
    {
        $this->testProperty = $value;

        return $this;
    }

    public function setTestObject($value)
    {
        $this->testObject = $value;

        return $this;
    }
}