<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;

abstract class CallbackTestPrototype extends \PHPUnit_Framework_TestCase
{
    const SIGNING_KEY   = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_ID     = 'CLIENT-112233';
    const PAYNET_ID     = 'PAYNET-112233';

    /**
     * @dataProvider testProcessCallbackApprovedProvider
     */
    public function testProcessCallbackApproved(array $callback)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $callbackResponse   = new CallbackResponse($callback);
        $this->signCallback($callbackResponse);

        $this->object->processCallback($paymentTransaction, $callbackResponse);

        $this->assertTrue($paymentTransaction->isApproved());
        $this->assertTrue($paymentTransaction->isFinished());
        $this->assertFalse($paymentTransaction->hasErrors());

        return array($paymentTransaction, $callbackResponse);
    }

    abstract public function testProcessCallbackApprovedProvider();

    /**
     * @dataProvider testProcessCallbackDeclinedProvider
     */
    public function testProcessCallbackDeclined(array $callback)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $callbackResponse   = new CallbackResponse($callback);
        $this->signCallback($callbackResponse);

        $this->object->processCallback($paymentTransaction, $callbackResponse);

        $this->assertTrue($paymentTransaction->isDeclined());
        $this->assertTrue($paymentTransaction->isFinished());
        $this->assertTrue($paymentTransaction->hasErrors());

        return array($paymentTransaction, $callbackResponse);
    }

    abstract public function testProcessCallbackDeclinedProvider();

    /**
     * @dataProvider testProcessCallbackErrorProvider
     */
    public function testProcessCallbackError(array $callback)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $callbackResponse   = new CallbackResponse($callback);
        $this->signCallback($callbackResponse);

        try
        {
            $this->object->processCallback($paymentTransaction, $callbackResponse);
        }
        catch (PaynetException $error)
        {
            $this->assertTrue($paymentTransaction->isError());
            $this->assertTrue($paymentTransaction->isFinished());
            $this->assertTrue($paymentTransaction->hasErrors());

            $this->assertEquals($callbackResponse->getErrorMessage(), $error->getMessage());
            $this->assertEquals($callbackResponse->getErrorCode(), $error->getCode());

            return array($paymentTransaction, $callbackResponse);
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
        $this->object->processCallback($this->getPaymentTransaction(), new CallbackResponse);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in CallbackResponse
     */
    public function testProcessCallbackWithEmptyFields()
    {
        $callbackResponse = new CallbackResponse(array
        (
            'status'            => 'approved',
            'orderid'           => '_',
            'client_orderid'    => '_'
        ));

        $this->signCallback($callbackResponse);

        $this->object->processCallback($this->getPaymentTransaction(), $callbackResponse);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some fields from CallbackResponse unequal properties from Payment
     */
    public function testProcessCallbackWithUnequalFields()
    {
        $callbackResponse = new CallbackResponse(array
        (
            'status'            => 'approved',
            'orderid'           => 'unequal',
            'merchant_order'    => 'unequal',
            'client_orderid'    => 'unequal'
        ));

        $this->signCallback($callbackResponse);

        $this->object->processCallback($this->getPaymentTransaction(), $callbackResponse);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Invalid callback status: 'processing'
     */
    public function testProcessCallbackWithInvalidStatus()
    {
        $callbackResponse = new CallbackResponse(array
        (
            'status'            => 'processing',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_ID,
            'merchant_order'    =>  self::CLIENT_ID,
            'client_orderid'    =>  self::CLIENT_ID,
        ));

        $this->signCallback($callbackResponse);

        $this->object->processCallback($this->getPaymentTransaction(), $callbackResponse);
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
            'payment'           =>  new Payment(array
            (
                'client_id'         =>  self::CLIENT_ID,
                'paynet_id'         =>  self::PAYNET_ID,
                'amount'            =>  0.99,
                'currency'          => 'USD',
            )),
            'queryConfig'       =>  new QueryConfig(array
            (
                'signing_key'       =>  self::SIGNING_KEY
            ))
        ));
    }

    /**
     * Creates control code and set in to callback
     *
     * @param       CallbackResponse        $callbackResponse       Callback object
     */
    protected function signCallback(CallbackResponse $callbackResponse)
    {
        $callbackResponse['control'] = sha1
        (
            $callbackResponse->getStatus() .
            $callbackResponse->getPaymentPaynetId() .
            $callbackResponse->getPaymentClientId() .
            self::SIGNING_KEY
        );
    }
}