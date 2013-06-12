<?php

namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Data\Order;

abstract class QueryTestPrototype extends \PHPUnit_Framework_TestCase
{
    const LOGIN             = 'test-login';
    const END_POINT         =  789;
    const SIGN_KEY          = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_ORDER_ID   = 'CLIENT-112233';
    const PAYNET_ORDER_ID   = 'PAYNET-112233';

    /**
     * @dataProvider testCreateRequestProvider
     */
    public function testCreateRequest($controlCode)
    {
        $order  = $this->getOrder();

        $request = $this->object->createRequest($order);

        $this->assertInstanceOf('PaynetEasy\Paynet\Transport\Request', $request);
        $this->assertNotNull($request['control']);
        $this->assertEquals($controlCode, $request['control']);
        $this->assertFalse($order->hasErrors());
    }

    abstract public function testCreateRequestProvider();

    /**
     * @dataProvider testProcessResponseFilteredProvider
     */
    public function testProcessResponseFiltered(array $response)
    {
        $order = $this->getOrder();

        $this->object->processResponse($order, new Response($response));

        $this->assertOrderStates($order, Order::STATE_END, Order::STATUS_DECLINED);
        $this->assertFalse($order->hasErrors());
    }

    abstract public function testProcessResponseFilteredProvider();

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        $order = $this->getOrder();

        $this->object->processResponse($order, new Response($response));

        $this->assertOrderStates($order, Order::STATE_PROCESSING, null);
        $this->assertFalse($order->hasErrors());
    }

    abstract public function testProcessResponseProcessingProvider();

    /**
     * @dataProvider testProcessResponseErrorProvider
     */
    public function testProcessResponseError(array $response)
    {
        $order = $this->getOrder();

        // Payment error after check
        $this->object->processResponse($order, new Response($response));

        $this->assertOrderStates($order, Order::STATE_END, Order::STATUS_ERROR);
        $this->assertOrderError($order, $response['error-message'], $response['error-code']);
    }

    abstract public function testProcessResponseErrorProvider();

    /**
     * Validates order state and status
     *
     * @param       string      $state      Order state
     * @param       string      $status     Order status
     */
    protected function assertOrderStates(Order $order, $state, $status)
    {
        $this->assertEquals($state, $order->getState());
        $this->assertEquals($status, $order->getStatus());
    }

    /**
     * Validates last order error
     *
     * @param       string      $errorMessage       Error message
     * @param       int         $errorCode          Error code
     */
    protected function assertOrderError($order, $errorMessage, $errorCode)
    {
        $this->assertTrue($order->hasErrors());

        $error = $order->getLastError();

        $this->assertInstanceOf('\PaynetEasy\Paynet\Exceptions\PaynetException', $error);
        $this->assertEquals($errorMessage, $error->getMessage());
        $this->assertEquals($errorCode, $error->getCode());
    }

    /**
     * @return      \PaynetEasy\Paynet\Data\Order
     */
    abstract protected function getOrder();

    /**
     * @return      array
     */
    protected function getConfig()
    {
        return array
        (
            'login'                 =>  self::LOGIN,
            'end_point'             =>  self::END_POINT,
            'control'               =>  self::SIGN_KEY,
            'redirect_url'          => 'https://example.com/redirect_url',
            'server_callback_url'   => 'https://example.com/callback_url'
        );
    }
}