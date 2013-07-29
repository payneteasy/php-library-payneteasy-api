<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use RuntimeException;

class PaymentTransaction extends Data
{
    /**
     * Payment transaction is new
     */
    const STATUS_NEW        = 'new';

    /**
     * Payment transaction is now processing
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * Payment transaction approved
     */
    const STATUS_APPROVED   = 'approved';

    /**
     * Payment transaction declined by bank
     */
    const STATUS_DECLINED   = 'declined';

    /**
     * Payment transaction declined by PaynetEasy filters
     */
    const STATUS_FILTERED   = 'filtered';

    /**
     * Payment transaction processed with error
     */
    const STATUS_ERROR      = 'error';

    /**
     * All allowed payment transaction statuses
     *
     * @var array
     */
    static protected $allowedStatuses = array
    (
        self::STATUS_PROCESSING,
        self::STATUS_APPROVED,
        self::STATUS_FILTERED,
        self::STATUS_DECLINED,
        self::STATUS_ERROR
    );

    /**
     * Transaction status
     *
     * @var string
     */
    protected $status = self::STATUS_NEW;

    /**
     * Payment transaction payment
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\Payment
     */
    protected $payment;

    /**
     * Payment query config
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     */
    protected $queryConfig;

    /**
     * Set payment transaction status
     *
     * @param       string      $status     Payment transaction status
     *
     * @return      self
     */
    public function setStatus($status)
    {
        if (!in_array($status, static::$allowedStatuses))
        {
            throw new RuntimeException("Unknown transaction status given: {$status}");
        }

        $this->status = $status;

        $this->getPayment()->setStatus($this->status);

        return $this;
    }

    /**
     * Get payment transaction status
     *
     * @return      string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * True, if payment transaction is new
     *
     * @return      string
     */
    public function isNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
    }

    /**
     * True, if payment transaction is now processing
     *
     * @return      boolean
     */
    public function isProcessing()
    {
        return $this->getStatus() == self::STATUS_PROCESSING;
    }

    /**
     * True, if payment transaction approved
     *
     * @return      boolean
     */
    public function isApproved()
    {
        return $this->getStatus() == self::STATUS_APPROVED;
    }

    /**
     * True, if payment transaction declined
     *
     * @return      boolean
     */
    public function isDeclined()
    {
        return in_array($this->getStatus(), array(self::STATUS_FILTERED, self::STATUS_DECLINED));
    }

    /**
     * True, if error occured when processing payment transaction
     *
     * @return      boolean
     */
    public function isError()
    {
        return $this->getStatus() == self::STATUS_ERROR;
    }

    /**
     * True, if payment transaction processing is finished
     *
     * @return      boolean
     */
    public function isFinished()
    {
        return !$this->isNew() && !$this->isProcessing();
    }

    /**
     * Set payment transaction payment
     *
     * @param       Payment         $payment        Payment
     *
     * @return      self
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment transaction payment
     *
     * @return      string
     */
    public function getPayment()
    {
        if (empty($this->payment))
        {
            $this->payment = new Payment;
        }

        return $this->payment;
    }

    /**
     * Set payment query config
     *
     * @param       QueryConfig         $queryConfig        Payment query config
     *
     * @return      self
     */
    public function setQueryConfig(QueryConfig $queryConfig)
    {
        $this->queryConfig = $queryConfig;

        return $this;
    }

    /**
     * Get payment query config
     *
     * @return      QueryConfig
     */
    public function getQueryConfig()
    {
        if (empty($this->queryConfig))
        {
            $this->queryConfig = new QueryConfig;
        }

        return $this->queryConfig;
    }
}