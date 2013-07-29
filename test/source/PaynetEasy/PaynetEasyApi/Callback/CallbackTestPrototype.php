<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
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
        $paymentTransaction = $this->getPaymentTransaction();

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($paymentTransaction, new CallbackResponse($callback));

        $this->assertPaymentStates($paymentTransaction, PaymentTransaction::STAGE_FINISHED, PaymentTransaction::STATUS_APPROVED);
    }

    abstract public function testProcessCallbackApprovedProvider();

    /**
     * @dataProvider testProcessCallbackDeclinedProvider
     */
    public function testProcessCallbackDeclined(array $callback)
    {
        $paymentTransaction = $this->getPaymentTransaction();

        $callback['control'] = $this->createSignature($callback);

        $this->object->processCallback($paymentTransaction, new CallbackResponse($callback));

        $this->assertPaymentStates($paymentTransaction, PaymentTransaction::STAGE_FINISHED, PaymentTransaction::STATUS_DECLINED);
    }

    abstract public function testProcessCallbackDeclinedProvider();

    /**
     * @dataProvider testProcessCallbackErrorProvider
     */
    public function testProcessCallbackError(array $callback)
    {
        $paymentTransaction = $this->getPaymentTransaction();

        $callback['control'] = $this->createSignature($callback);

        try
        {
            // Payment error after check
            $this->object->processCallback($paymentTransaction, new CallbackResponse($callback));
        }
        catch (PaynetException $error)
        {
            $this->assertPaymentStates($paymentTransaction, PaymentTransaction::STAGE_FINISHED, PaymentTransaction::STATUS_ERROR);

            $this->assertEquals($callback['error_message'], $error->getMessage());
            $this->assertEquals($callback['error_code'], $error->getCode());
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
        $paymentTransaction = $this->getPaymentTransaction();

        $this->object->processCallback($paymentTransaction, new CallbackResponse);
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

        $this->object->processCallback($this->getPaymentTransaction(), new CallbackResponse($callback));
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

        $this->object->processCallback($this->getPaymentTransaction(), new CallbackResponse($callback));
    }

    /**
     * Validates payment transport stage and bank status
     *
     * @param       string      $processingStage        Payment transport stage
     * @param       string      $status                 Payment bank status
     */
    protected function assertPaymentStates(PaymentTransaction $paymentTransaction, $processingStage, $status)
    {
        $this->assertEquals($processingStage, $paymentTransaction->getProcessingStage());
        $this->assertEquals($status, $paymentTransaction->getStatus());
    }

    /**
     * Get payment for test
     *
     * @return      \PaynetEasy\PaynetEasyApi\PaymentData\Payment       Payment for test
     */
    protected function getPaymentTransaction()
    {
        return new PaymentTransaction(array
        (
            'payment'                   =>  new Payment(array
            (
                'client_payment_id'         =>  self::CLIENT_PAYMENT_ID,
                'paynet_payment_id'         =>  self::PAYNET_PAYMENT_ID,
                'amount'                    =>  0.99,
                'currency'                  => 'USD',
            )),
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