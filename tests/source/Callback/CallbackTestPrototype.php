<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Transport\CallbackResponse;
use PaynetEasy\Paynet\OrderData\Order;

abstract class CallbackTestPrototype extends \PHPUnit_Framework_TestCase
{
    const SIGN_KEY          = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_ORDER_ID   = 'CLIENT-112233';
    const PAYNET_ORDER_ID   = 'PAYNET-112233';

    /**
     * @dataProvider testProcessCallbackApprovedProvider
     */
    public function testProcessCallbackApproved(array $callback)
    {
        $order = $this->getOrder();

        $callback['control'] = $this->createControlCode($callback);

        $this->object->processCallback($order, new CallbackResponse($callback));

        $this->assertOrderStates($order, Order::STATE_END, Order::STATUS_APPROVED);
        $this->assertFalse($order->hasErrors());
    }

    abstract public function testProcessCallbackApprovedProvider();

    /**
     * @dataProvider testProcessCallbackDeclinedProvider
     */
    public function testProcessCallbackDeclined(array $callback)
    {
        $order = $this->getOrder();

        $callback['control'] = $this->createControlCode($callback);

        $this->object->processCallback($order, new CallbackResponse($callback));

        $this->assertOrderStates($order, Order::STATE_END, Order::STATUS_DECLINED);
        $this->assertTrue($order->hasErrors());
    }

    abstract public function testProcessCallbackDeclinedProvider();

    /**
     * @dataProvider testProcessCallbackErrorProvider
     */
    public function testProcessCallbackError(array $callback)
    {
        $order = $this->getOrder();

        $callback['control'] = $this->createControlCode($callback);

        // Payment error after check
        $this->object->processCallback($order, new CallbackResponse($callback));

        $this->assertOrderStates($order, Order::STATE_END, Order::STATUS_ERROR);
        $this->assertOrderError($order, $callback['error_message'], $callback['error_code']);
    }

    abstract public function testProcessCallbackErrorProvider();

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

        $this->assertInstanceOf('\PaynetEasy\Paynet\Exception\PaynetException', $error);
        $this->assertEquals($errorMessage, $error->getMessage());
        $this->assertEquals($errorCode, $error->getCode());
    }

    /**
     * @return      \PaynetEasy\Paynet\OrderData\Order
     */
    protected function getOrder()
    {
        return new Order(array
        (
            'client_orderid'                =>  self::CLIENT_ORDER_ID,
            'amount'                    =>  0.99,
            'currency'                  => 'USD'
        ));
    }

    /**
     * @return      array
     */
    protected function getConfig()
    {
        return array('control' =>  self::SIGN_KEY);
    }

    /**
     * Creates control for callback
     *
     * @param       array       $callback
     *
     * @return      string
     */
    protected function createControlCode(array $callback)
    {
        return sha1
        (
            $callback['status'] .
            $callback['orderid'] .
            $callback['client_orderid'] .
            self::SIGN_KEY
        );
    }
}