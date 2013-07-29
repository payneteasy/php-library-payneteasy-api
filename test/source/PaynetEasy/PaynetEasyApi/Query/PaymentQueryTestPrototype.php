<?php

namespace PaynetEasy\PaynetEasyApi\Query;

abstract class PaymentQueryTestPrototype extends QueryTestPrototype
{
    /**
     * Query payment status
     *
     * @var string
     */
    protected $paymentStatus;

    protected $successType = 'async-response';

    /**
     * @dataProvider testCreateRequestProvider
     */
    public function testCreateRequest($controlCode)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $payment            = $paymentTransaction->getPayment();

        $request        = $this->object->createRequest($paymentTransaction);
        $requestFields  = $request->getRequestFields();

        $this->assertInstanceOf('PaynetEasy\PaynetEasyApi\Transport\Request', $request);
        $this->assertNotNull($request->getApiMethod());
        $this->assertNotNull($request->getEndPoint());
        $this->assertNotNull($requestFields['control']);
        $this->assertEquals($controlCode, $requestFields['control']);
        $this->assertEquals($this->paymentStatus, $payment->getStatus());
        $this->assertTrue($payment->hasProcessingTransaction());
    }

    public function testProcessResponseDeclinedProvider()
    {
        return array(
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'filtered',
            'paynet-order-id'   =>  self::PAYNET_PAYMENT_ID,
            'merchant-order-id' =>  self::CLIENT_PAYMENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        =>  8876
        )),
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'declined',
            'paynet-order-id'   =>  self::PAYNET_PAYMENT_ID,
            'merchant-order-id' =>  self::CLIENT_PAYMENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        =>  578
        )));
    }

    public function testProcessResponseProcessingProvider()
    {
        return array(array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'processing',
            'paynet-order-id'   =>  self::PAYNET_PAYMENT_ID,
            'merchant-order-id' =>  self::CLIENT_PAYMENT_ID,
            'serial-number'     =>  md5(time())
        )));
    }

    public function testProcessResponseErrorProvider()
    {
        return array(
        array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'error',
            'paynet-order-id'   =>  self::PAYNET_PAYMENT_ID,
            'merchant-order-id' =>  self::CLIENT_PAYMENT_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        =>  2
        )),
        array(array
        (
            'type'              => 'validation-error',
            'serial-number'     =>  md5(time()),
            'error-message'     => 'validation-error message',
            'error-code'        =>  1
        )),
        array(array
        (
            'type'              => 'error',
            'error-message'     => 'test type error message',
            'error-code'        =>  5
        )));
    }
}