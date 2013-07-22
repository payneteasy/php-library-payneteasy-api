<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;

abstract class QueryTestPrototype extends \PHPUnit_Framework_TestCase
{
    const LOGIN                     = 'test-login';
    const END_POINT                 =  789;
    const SIGNING_KEY               = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_PAYMENT_ID         = 'CLIENT-112233';
    const PAYNET_PAYMENT_ID         = 'PAYNET-112233';

    const RECURRENT_CARD_FROM_ID    = '5588943';
    const RECURRENT_CARD_TO_ID      = '5588978';

    /**
     * @dataProvider testCreateRequestProvider
     */
    public function testCreateRequest($controlCode)
    {
        $payment  = $this->getPayment();

        $request        = $this->object->createRequest($payment);
        $requestFields  = $request->getRequestFields();


        $this->assertInstanceOf('PaynetEasy\PaynetEasyApi\Transport\Request', $request);
        $this->assertNotNull($request->getApiMethod());
        $this->assertNotNull($request->getEndPoint());
        $this->assertNotNull($requestFields['control']);
        $this->assertEquals($controlCode, $requestFields['control']);
        $this->assertFalse($payment->hasErrors());
    }

    abstract public function testCreateRequestProvider();

    /**
     * @dataProvider testProcessResponseDeclinedProvider
     */
    public function testProcessResponseDeclined(array $response)
    {
        $payment = $this->getPayment();

        $this->object->processResponse($payment, new Response($response));

        $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_DECLINED);
        $this->assertTrue($payment->hasErrors());
    }

    abstract public function testProcessResponseDeclinedProvider();

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        $payment = $this->getPayment();

        $this->object->processResponse($payment, new Response($response));

        $this->assertPaymentStates($payment, Payment::STAGE_CREATED, Payment::STATUS_PROCESSING);
        $this->assertFalse($payment->hasErrors());
    }

    abstract public function testProcessResponseProcessingProvider();

    /**
     * @dataProvider testProcessResponseErrorProvider
     */
    public function testProcessResponseError(array $response)
    {
        $payment = $this->getPayment();

        try
        {
            // Payment error after check
            $this->object->processResponse($payment, new Response($response));
        }
        catch (PaynetException $error)
        {
            $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_ERROR);
            $this->assertPaymentError($payment, $response['error-message'], $response['error-code']);
            $this->assertInstanceOf('\PaynetEasy\PaynetEasyApi\Exception\PaynetException', $error);

            return;
        }

        $this->fail('Exception must be throwned');
    }

    abstract public function testProcessResponseErrorProvider();

    /**
     * Validates payment transport stage and bank status
     *
     * @param       string      $processingStage     Payment transport stage
     * @param       string      $status             Payment bank status
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
    abstract protected function getPayment();

    /**
     * Get query config
     *
     * @return      array
     */
    protected function getConfig()
    {
        return new QueryConfig(array
        (
            'login'             =>  self::LOGIN,
            'end_point'         =>  self::END_POINT,
            'signing_key'       =>  self::SIGNING_KEY,
            'site_url'          => 'http://example.com',
            'redirect_url'      => 'https://example.com/redirect_url',
            'callback_url'      => 'https://example.com/callback_url'
        ));
    }
}