<?php

namespace PaynetEasy\PaynetEasyApi\Query;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-11 at 17:21:40.
 */
class QueryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QueryFactory;
    }

    public function testGetQuery()
    {
        $this->assertInstanceOf('PaynetEasy\PaynetEasyApi\Query\CreateCardRefQuery',
                                $this->object->getQuery('create-card-ref'));

        $this->assertInstanceOf('PaynetEasy\PaynetEasyApi\Query\ReturnQuery',
                                $this->object->getQuery('return'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown query class 'PaynetEasy\PaynetEasyApi\Query\UnknownQuery' for query with name 'unknown'
     */
    public function testGetQueryWithUnknownClass()
    {
        $this->object->getQuery('unknown', array());
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Query class 'PaynetEasy\PaynetEasyApi\Query\NotqueryQuery' does not implements 'PaynetEasy\PaynetEasyApi\Query\QueryInterface' interface
     */
    public function testGetQueryWithNotQueryClass()
    {
        $this->object->getQuery('notquery', array());
    }
}
