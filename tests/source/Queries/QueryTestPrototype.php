<?php

namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Data\Order;

abstract class QueryTestPrototype extends \PHPUnit_Framework_TestCase
{
    abstract public function testCreateRequest();

    /**
     * @dataProvider testProcessResponseApprovedProvider
     */
    public function testProcessResponseApproved(array $response)
    {
        $this->object->processResponse($this->order, new Response($response));

        $this->assertOrderStates(Order::STATE_END, Order::STATUS_APPROVED);
        $this->assertFalse($this->order->hasErrors());
    }

    abstract public function testProcessResponseApprovedProvider();

    /**
     * @dataProvider testProcessResponseDeclinedProvider
     */
    public function testProcessResponseDeclined(array $response)
    {
        $this->object->processResponse($this->order, new Response($response));

        $this->assertOrderStates(Order::STATE_END, Order::STATUS_DECLINED);
        $this->assertFalse($this->order->hasErrors());
    }

    abstract public function testProcessResponseDeclinedProvider();

    /**
     * @dataProvider testProcessResponseFilteredProvider
     */
    public function testProcessResponseFiltered(array $response)
    {
        $this->object->processResponse($this->order, new Response($response));

        $this->assertOrderStates(Order::STATE_END, Order::STATUS_DECLINED);
        $this->assertFalse($this->order->hasErrors());
    }

    abstract public function testProcessResponseFilteredProvider();

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        $this->object->processResponse($this->order, new Response($response));

        $this->assertOrderStates(Order::STATE_PROCESSING, null);
        $this->assertFalse($this->order->hasErrors());
    }

    abstract public function testProcessResponseProcessingProvider();

    /**
     * @dataProvider testProcessResponseErrorProvider
     */
    public function testProcessResponseError(array $response)
    {
        // Payment error after check
        $this->object->processResponse($this->order, new Response($response));

        $this->assertOrderStates(Order::STATE_END, Order::STATUS_ERROR);
        $this->assertOrderError($response['error-message'], $response['error-code']);
    }

    abstract public function testProcessResponseErrorProvider();

    /**
     * Validates order state and status
     *
     * @param       string      $state      Order state
     * @param       string      $status     Order status
     */
    protected function assertOrderStates($state, $status)
    {
        $this->assertEquals($state, $this->order->getState());
        $this->assertEquals($status, $this->order->getStatus());
    }

    /**
     * Validates last order error
     *
     * @param       string      $errorMessage       Error message
     * @param       int         $errorCode          Error code
     */
    protected function assertOrderError($errorMessage, $errorCode)
    {
        $this->assertTrue($this->order->hasErrors());

        $error = $this->order->getLastError();

        $this->assertInstanceOf('\PaynetEasy\Paynet\Exceptions\PaynetException', $error);
        $this->assertEquals($error->getMessage(), $errorMessage);
        $this->assertEquals($error->getCode(), $errorCode);
    }
}