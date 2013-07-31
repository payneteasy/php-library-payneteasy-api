<?php

namespace PaynetEasy\PaynetEasyApi\Query\Prototype;

use PaynetEasy\PaynetEasyApi\Transport\Response;

abstract class SyncQueryTest extends QueryTest
{
    /**
     * @dataProvider testProcessResponseApprovedProvider
     */
    public function testProcessResponseApproved(array $response)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $responseObject     = new Response($response);

        $this->object->processResponse($paymentTransaction, $responseObject);

        $this->assertTrue($paymentTransaction->isApproved());
        $this->assertTrue($paymentTransaction->isFinished());
        $this->assertFalse($paymentTransaction->hasErrors());

        return array($paymentTransaction, $responseObject);
    }

    abstract public function testProcessResponseApprovedProvider();

    /**
     * @dataProvider testProcessResponseDeclinedProvider
     */
    public function testProcessResponseDeclined(array $response)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $responseObject     = new Response($response);

        $this->object->processResponse($paymentTransaction, $responseObject);

        $this->assertTrue($paymentTransaction->isDeclined());
        $this->assertTrue($paymentTransaction->isFinished());
        $this->assertTrue($paymentTransaction->hasErrors());

        return array($paymentTransaction, $responseObject);
    }

    public function testProcessResponseDeclinedProvider()
    {
        return array(
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'filtered',
            'paynet-order-id'   =>  self::PAYNET_ID,
            'merchant-order-id' =>  self::CLIENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        =>  8876
        )),
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'declined',
            'paynet-order-id'   =>  self::PAYNET_ID,
            'merchant-order-id' =>  self::CLIENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        =>  578
        )));
    }

    public function testProcessResponseErrorProvider()
    {
        return array(
        // Payment error after check
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'error',
            'paynet-order-id'   =>  self::PAYNET_ID,
            'merchant-order-id' =>  self::CLIENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'status error message',
            'error-code'        =>  24
        )),
        // Validation error
        array(array
        (
            'type'              => 'validation-error',
            'error-message'     => 'validation error message',
            'error-code'        =>  1
        )),
        // Immediate payment error
        array(array
        (
            'type'              => 'error',
            'error-message'     => 'immediate error message',
            'error-code'        =>  1
        )));
    }
}