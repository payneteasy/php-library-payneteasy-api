<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;

abstract class CallbackTestPrototype extends \PHPUnit_Framework_TestCase
{
    const SIGNING_KEY           = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_PAYMENT_ID     = 'CLIENT-112233';
    const PAYNET_PAYMENT_ID     = 'PAYNET-112233';

    /**
     * @dataProvider testProcessCallbackApprovedProvider
     */
    public function testProcessCallbackApproved(array $callback)
    {
        $payment = $this->getPayment();

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($payment, new CallbackResponse($callback));

        $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_APPROVED);
        $this->assertFalse($payment->hasErrors());
    }

    abstract public function testProcessCallbackApprovedProvider();

    /**
     * @dataProvider testProcessCallbackDeclinedProvider
     */
    public function testProcessCallbackDeclined(array $callback)
    {
        $payment = $this->getPayment();

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($payment, new CallbackResponse($callback));

        $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_DECLINED);
        $this->assertTrue($payment->hasErrors());
    }

    abstract public function testProcessCallbackDeclinedProvider();

    /**
     * @dataProvider testProcessCallbackErrorProvider
     */
    public function testProcessCallbackError(array $callback)
    {
        $payment = $this->getPayment();

        $callback['control'] = $this->createSignature($callback);

        try
        {
            // Payment error after check
            $this->object->processCallback($payment, new CallbackResponse($callback));
        }
        catch (PaynetException $error)
        {
            $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_ERROR);
            $this->assertPaymentError($payment, $callback['error_message'], $callback['error_code']);
            $this->assertInstanceOf('\PaynetEasy\PaynetEasyApi\Exception\PaynetException', $error);

            return;
        }

        $this->fail('Exception must be throwned');
    }

    abstract public function testProcessCallbackErrorProvider();

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Actual control code '' does not equal expected
     */
    public function testProcessCallbackWithEmptyControl()
    {
        $payment = $this->getPayment();

        $this->object->processCallback($payment, new CallbackResponse);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in CallbackResponse
     */
    public function testProcessCallbackWithEmptyFields()
    {
        $callback = array
        (
            'status'            => 'approved',
            'orderid'           => '_',
            'client_orderid'    => '_'
        );

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($this->getPayment(), new CallbackResponse($callback));
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some fields from CallbackResponse unequal properties from Payment
     */
    public function testProcessCallbackWithUnequalFields()
    {
        $callback = array
        (
            'status'            => 'approved',
            'orderid'           => 'unequal',
            'merchant_order'    => 'unequal',
            'client_orderid'    => 'unequal'
        );

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($this->getPayment(), new CallbackResponse($callback));
    }

    /**
     * Validates payment transport stage and bank status
     *
     * @param       string      $processingStage        Payment transport stage
     * @param       string      $status                 Payment bank status
     */
    protected function assertPaymentStates(Payment $payment, $processingStage, $status)
    {
        $this->assertEquals($processingStage, $payment->getProcessingStage());
        $this->assertEquals($status, $payment->getStatus());
    }

    /**
     * Validates last payment error
     *
     * @param       string      $errorMessage       Error message
     * @param       int         $errorCode          Error code
     */
    protected function assertPaymentError($payment, $errorMessage, $errorCode)
    {
        $this->assertTrue($payment->hasErrors());

        $error = $payment->getLastError();

        $this->assertInstanceOf('\PaynetEasy\PaynetEasyApi\Exception\PaynetException', $error);
        $this->assertEquals($errorMessage, $error->getMessage());
        $this->assertEquals($errorCode, $error->getCode());
    }

    /**
     * Get payment for test
     *
     * @return      \PaynetEasy\PaynetEasyApi\PaymentData\Payment       Payment for test
     */
    protected function getPayment()
    {
        return new Payment(array
        (
            'client_payment_id'         =>  self::CLIENT_PAYMENT_ID,
            'paynet_payment_id'         =>  self::PAYNET_PAYMENT_ID,
            'amount'                    =>  0.99,
            'currency'                  => 'USD',
            'queryConfig'               =>  new QueryConfig(array
            (
                'signing_key'               =>  self::SIGNING_KEY
            ))
        ));
    }

    /**
     * Creates control for callback
     *
     * @param       array       $callback       Callback data
     *
     * @return      string                      Callback control code
     */
    protected function createSignature(array $callback)
    {
        return sha1
        (
            $callback['status'] .
            $callback['orderid'] .
            $callback['client_orderid'] .
            self::SIGNING_KEY
        );
    }
}