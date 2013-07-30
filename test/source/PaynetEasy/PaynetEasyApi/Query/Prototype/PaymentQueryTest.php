<?php

namespace PaynetEasy\PaynetEasyApi\Query\Prototype;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;

abstract class PaymentQueryTest extends QueryTest
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
        list($paymentTransaction, $request) = parent::testCreateRequest($controlCode);

        $payment        = $paymentTransaction->getPayment();

        $this->assertEquals($this->paymentStatus, $payment->getStatus());
        $this->assertTrue($payment->hasProcessingTransaction());
        $this->assertEquals(PaymentTransaction::PROCESSOR_QUERY, $paymentTransaction->getProcessorType());
        $this->assertEquals($this->readAttribute($this->object, 'apiMethod'), $paymentTransaction->getProcessorName());

        return array($paymentTransaction, $request);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Payment can not has processing payment transaction
     */
    public function testCreateRequestWithProcessingPayment()
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $paymentTransaction->setStatus(PaymentTransaction::STATUS_PROCESSING);

        $this->object->createRequest($paymentTransaction);
    }

    public function testProcessResponseProcessingProvider()
    {
        return array(array(array
        (
            'type'              =>  $this->successType,
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