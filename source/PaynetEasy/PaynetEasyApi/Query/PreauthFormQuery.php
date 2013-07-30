<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentFormQuery;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

class PreauthFormQuery extends PaymentFormQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_PREAUTH;
}