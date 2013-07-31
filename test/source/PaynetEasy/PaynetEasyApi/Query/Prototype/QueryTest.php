<?php

namespace PaynetEasy\PaynetEasyApi\Query\Prototype;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\PaynetException;

abstract class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryInterface
     */
    protected $object;

    protected $successType;

    const LOGIN                     = 'test-login';
    const END_POINT                 =  789;
    const SIGNING_KEY               = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_ID                 = 'CLIENT-112233';
    const PAYNET_ID                 = 'PAYNET-112233';

    const RECURRENT_CARD_FROM_ID    = '5588943';
    const RECURRENT_CARD_TO_ID      = '5588978';

    /**
     * @dataProvider testCreateRequestProvider
     */
    public function testCreateRequest($controlCode)
    {
        $paymentTransaction  = $this->getPaymentTransaction();

        $request        = $this->object->createRequest($paymentTransaction);
        $requestFields  = $request->getRequestFields();


        $this->assertInstanceOf('PaynetEasy\PaynetEasyApi\Transport\Request', $request);
        $this->assertNotNull($request->getApiMethod());
        $this->assertNotNull($request->getEndPoint());
        $this->assertNotNull($requestFields['control']);
        $this->assertEquals($controlCode, $requestFields['control']);
        $this->assertFalse($paymentTransaction->hasErrors());

        return array($paymentTransaction, $request);
    }

    abstract public function testCreateRequestProvider();

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Payment
     */
    public function testCreateRequestWithEmptyFields()
    {
        $paymentTransaction = new PaymentTransaction(array
        (
            'query_config'  => new QueryConfig(array
            (
                'signing_key'   => self::SIGNING_KEY
            ))
        ));

        $this->object->createRequest($paymentTransaction);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some fields invalid in Payment
     */
    public function testCreateRequestWithInvalidFields()
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $paymentTransaction->getPayment()->setClientId('123456789012345678901234567890');
        $paymentTransaction->getQueryConfig()->setLogin('123456789012345678901234567890123456789012345678901234567890');

        $this->object->createRequest($paymentTransaction);
    }

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $responseObject     = new Response($response);

        $this->object->processResponse($paymentTransaction, $responseObject);

        $this->assertTrue($paymentTransaction->isProcessing());
        $this->assertFalse($paymentTransaction->isFinished());
        $this->assertFalse($paymentTransaction->hasErrors());

        return array($paymentTransaction, $responseObject);
    }

    abstract public function testProcessResponseProcessingProvider();

    /**
     * @dataProvider testProcessResponseErrorProvider
     */
    public function testProcessResponseError(array $response)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $responseObject     = new Response($response);

        try
        {
            // Payment error after check
            $this->object->processResponse($paymentTransaction, $responseObject);
        }
        catch (PaynetException $error)
        {
            $this->assertTrue($paymentTransaction->isError());
            $this->assertTrue($paymentTransaction->isFinished());
            $this->assertTrue($paymentTransaction->hasErrors());

            $this->assertEquals($response['error-message'], $error->getMessage());
            $this->assertEquals($response['error-code'], $error->getCode());

            return array($paymentTransaction, $responseObject);
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
        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Response
     */
    public function testProcessSuccessResponseWithEmptyFields()
    {
        $response = new Response(array('type' => $this->successType));
        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientId '_' does not match Payment clientId
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

        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Unknown response type
     */
    public function testProcessErrorResponseWithoutType()
    {
        $response = new Response(array('status'    => 'error'));
        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientId 'invalid' does not match Payment clientId
     */
    public function testProcessErrorResponseWithInvalidId()
    {
        $response = new Response(array
        (
            'type'              => 'error',
            'client_orderid'    => 'invalid'
        ));

        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * Get payment for test
     *
     * @return      \PaynetEasy\PaynetEasyApi\PaymentData\Payment       Payment for test
     */
    abstract protected function getPayment();

    /**
     * Get payment transaction for test
     *
     * @return      \PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction
     */
    protected function getPaymentTransaction()
    {
        return new PaymentTransaction(array
        (
            'payment'       => $this->getPayment(),
            'query_config'  => $this->getConfig()
        ));
    }

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