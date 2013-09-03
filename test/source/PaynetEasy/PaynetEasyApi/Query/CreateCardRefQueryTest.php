<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\SyncQueryTest;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-12 at 16:43:22.
 */
class CreateCardRefQueryTest extends SyncQueryTest
{
    protected $successType = 'create-card-ref-response';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CreateCardRefQuery('_');
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Payment
     */
    public function testCreateRequestWithEmptyFields()
    {
        $paymentTransaction = new PaymentTransaction(array
        (
            'payment'       => new Payment(array
            (
                'status'        => Payment::STATUS_CAPTURE
            )),
            'query_config'  => new QueryConfig(array
            (
                'signing_key'   => self::SIGNING_KEY
            )),
            'status'        => PaymentTransaction::STATUS_APPROVED
        ));

        $this->object->createRequest($paymentTransaction);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Only finished payment transaction can be used for create-card-ref-id
     */
    public function testCreateRequestWithNotEndedPayment()
    {
        $this->object->createRequest(parent::getPaymentTransaction());
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Can not use new payment for create-card-ref-id
     */
    public function testCreateRequestWithNewPayment()
    {
        $payment = new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'paynet_id'             => self::PAYNET_ID
        ));

        $paymentTransaction = $this->getPaymentTransaction();
        $paymentTransaction->setPayment($payment);

        $this->object->createRequest($paymentTransaction);
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::LOGIN .
                self::CLIENT_ID .
                self::PAYNET_ID .
                self::SIGNING_KEY
            ),
            'recurrent'
        ));
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Some required fields missed or empty in Response: card-ref-id
     */
    public function testProcessResponseWithEmptyFields()
    {
        $paymentTransaction = $this->getPaymentTransaction();

        $this->object->processResponse($paymentTransaction, new Response(array
        (
            'type'              =>  $this->successType,
            'status'            => 'processing',
            'paynet-order-id'   =>  self::PAYNET_ID,
            'merchant-order-id' =>  self::CLIENT_ID,
            'serial-number'     =>  md5(time())
        )));
    }

    public function testProcessResponseApprovedProvider()
    {
        return array(array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'approved',
            'card-ref-id'       =>  self::RECURRENT_CARD_FROM_ID,
            'serial-number'     =>  md5(time())
        )));
    }

    /**
     * {@inheritdoc}
     */
    protected function getPaymentTransaction()
    {
        return parent::getPaymentTransaction()
            ->setStatus(PaymentTransaction::STATUS_APPROVED);
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'paynet_id'             => self::PAYNET_ID,
            'status'                => Payment::STATUS_PREAUTH
        ));
    }
}
