<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;

abstract class QueryTestPrototype extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryInterface
     */
    protected $object;

    protected $successType;

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
    }

    abstract public function testCreateRequestProvider();


    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Payment
     */
    public function testCreateRequestWithEmptyFields()
    {
        $payment = new Payment(array
        (
            'query_config'  => new QueryConfig(array
            (
                'signing_key'   => self::SIGNING_KEY
            ))
        ));

        $this->object->createRequest($payment);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some fields invalid in Payment
     */
    public function testCreateRequestWithInvalidFields()
    {
        $payment = $this->getPayment();
        $payment->setClientPaymentId('123456789012345678901234567890');

        $this->object->createRequest($payment);
    }

    /**
     * @dataProvider testProcessResponseDeclinedProvider
     */
    public function testProcessResponseDeclined(array $response)
    {
        $payment = $this->getPayment();

        $this->object->processResponse($payment, new Response($response));

        $this->assertPaymentStates($payment, Payment::STAGE_FINISHED, Payment::STATUS_DECLINED);
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

            $this->assertEquals($response['error-message'], $error->getMessage());
            $this->assertEquals($response['error-code'], $error->getCode());
            $this->assertInstanceOf('\PaynetEasy\PaynetEasyApi\Exception\PaynetException', $error);

            return;
        }

        $this->fail('Exception must be throwned');
    }

    abstract public function testProcessResponseErrorProvider();

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response type 'invalid' does not match success response type
     */
    public function testProcessSuccessResponseWithInvalidType()
    {
        $response = new Response(array('type' => 'invalid'));
        $this->object->processResponse($this->getPayment(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Response
     */
    public function testProcessSuccessResponseWithEmptyFields()
    {
        $response = new Response(array('type' => $this->successType));
        $this->object->processResponse($this->getPayment(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientPaymentId '_' does not match Payment clientPaymentId
     */
    public function testProcessSuccessResponseWithInvalidId()
    {
        $response = new Response(array
        (
            'type'              => $this->successType,
            'paynet-order-id'   => '_',
            'merchant-order-id' => '_',
            'serial-number'     => '_',
            'card-ref-id'       => '_',
            'redirect-url'      => '_',
            'client_orderid'    => 'invalid'
        ));

        $this->object->processResponse($this->getPayment(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Unknown response type
     */
    public function testProcessErrorResponseWithoutType()
    {
        $response = new Response(array('status'    => 'error'));
        $this->object->processResponse($this->getPayment(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientPaymentId 'invalid' does not match Payment clientPaymentId
     */
    public function testProcessErrorResponseWithInvalidId()
    {
        $response = new Response(array
        (
            'type'              => 'error',
            'client_orderid'    => 'invalid'
        ));

        $this->object->processResponse($this->getPayment(), $response);
    }

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