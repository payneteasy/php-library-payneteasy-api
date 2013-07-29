<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

class SaleFormQuery extends AbstractFormQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_CAPTURE;
}