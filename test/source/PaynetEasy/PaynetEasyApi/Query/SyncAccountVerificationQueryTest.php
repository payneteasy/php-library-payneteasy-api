<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\SyncQueryTest;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;

class SyncAccountVerificationQueryTest extends SyncQueryTest {

    /**
     * @var SyncAccountVerificationQuery
     */
    protected $object;
    protected $successType = 'status-response';

    protected function setUp()
    {
        $this->object = new SyncAccountVerificationQuery('_');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::END_POINT .
                self::CLIENT_ID .
                'vass.pupkin@example.com' .     // customer email
                self::SIGNING_KEY
            )
        ));
    }

    public function testProcessResponseApprovedProvider()
    {
        return array(array(array
        (
            'type'                      =>  $this->successType,
            'status'                    => 'approved',
            'paynet-order-id'           =>  self::PAYNET_ID,
            'merchant-order-id'         =>  self::CLIENT_ID,
            'email'                     => 'vass.pupkin@example.com',
            'phone'                     => '660-485-6353',
            'cardholder-name'           => 'Vasya Pupkin',
            'card-exp-month'            => '12',
            'card-exp-year'             => '2015',
            'last-four-digits'          => '8206',
            'card-type'                 => 'AMEX',
            'card-hash-id'              => '395954',
            'card-ref-id'               => '10901',
            'bin'                       => '377679',
        )));
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'description'           => 'This is test payment',
            'customer'              =>  new Customer(array
            (
                'email'                 => 'vass.pupkin@example.com',
                'ip_address'            => '127.0.0.1'
            )),
            'billing_address'       =>  new BillingAddress(array
            (
                'country'               => 'US',
                'state'                 => 'TX',
                'city'                  => 'Houston',
                'first_line'            => '2704 Colonial Drive',
                'zip_code'              => '1235',
                'phone'                 => '660-485-6353'
            )),
            'credit_card'           =>  new CreditCard(array
            (
                'card_printed_name'     => 'Vasya Pupkin',
                'credit_card_number'    => '3776 7964 0568 206',
                'expire_month'          => '12',
                'expire_year'           => '15',
                'cvv2'                  => '123'
            ))
        ));
    }
}