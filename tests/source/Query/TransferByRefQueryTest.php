<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderData\RecurrentCard;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-18 at 22:44:49.
 */
class TransferByRefQueryTest extends QueryTestPrototype
{
    /**
     * @var TransferByRefQuery
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TransferByRefQuery($this->getConfig());
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::LOGIN .
                self::CLIENT_ORDER_ID .
                self::RECURRENT_CARD_FROM_ID .
                self::RECURRENT_CARD_TO_ID .
                 9910 .
                'EUR' .
                self::SIGN_KEY
            )
        ));
    }

    public function testProcessResponseApprovedProvider()
    {
        return array(array(array
        (
            'type'              => 'async-response',
            'status'            => 'approved',
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time())
        )));
    }

    public function testProcessResponseDeclinedProvider()
    {
        return array(
        array(array
        (
            'type'              => 'async-response',
            'status'            => 'filtered',
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        =>  8876
        )),
        array(array
        (
            'type'              => 'async-response',
            'status'            => 'declined',
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        =>  578
        )));
    }

    public function testProcessResponseProcessingProvider()
    {
        return array(array(array
        (
            'type'              => 'async-response',
            'status'            => 'processing',
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time())
        )));
    }

    public function testProcessResponseErrorProvider()
    {
        return array(
        // Payment error after check
        array(array
        (
            'type'              => 'async-response',
            'status'            => 'error',
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'status error message',
            'error-code'        =>  2
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

    public function getOrder()
    {
        $order = new Order(array
        (
            'client_orderid'            =>  self::CLIENT_ORDER_ID,
            'order_desc'                => 'This is test order',
            'amount'                    =>  99.1,
            'currency'                  => 'EUR',
            'ipaddress'                 => '127.0.0.1',
            'site_url'                  => 'http://example.com'
        ));

        $sourceCard = new RecurrentCard(array
        (
            'cardrefid' => self::RECURRENT_CARD_FROM_ID,
            'cvv2' => 123
        ));

        $destCard = new RecurrentCard(array
        (
            'cardrefid' => self::RECURRENT_CARD_TO_ID,
        ));

        return $order->setRecurrentCardFrom($sourceCard)
                     ->setRecurrentCardTo($destCard);
    }
}
