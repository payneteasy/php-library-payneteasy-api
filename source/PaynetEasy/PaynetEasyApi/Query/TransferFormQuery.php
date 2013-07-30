<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentFormQuery;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

class TransferFormQuery extends PaymentFormQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_CAPTURE;
}