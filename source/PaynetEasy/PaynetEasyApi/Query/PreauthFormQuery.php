<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

class PreauthFormQuery extends AbstractFormQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_PREAUTH;
}