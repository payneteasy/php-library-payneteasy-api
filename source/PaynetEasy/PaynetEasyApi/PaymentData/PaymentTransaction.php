<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use RuntimeException;
use Exception;

class PaymentTransaction extends Data
{
    /**
     * Payment transaction processed by payment query
     */
    const PROCESSOR_QUERY        = 'query';

    /**
     * Payment transaction processed by PaynetEasy callback
     */
    const PROCESSOR_CALLBACK     = 'callback';

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
     * All allowed callback types
     *
     * @var array
     */
    static protected $allowedProcessorTypes = array
    (
        self::PROCESSOR_QUERY,
        self::PROCESSOR_CALLBACK
    );

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
     * Payment transaction processor type
     *
     * @var string
     */
    protected $processorType;

    /**
     * Payment transaction processor name
     *
     * @var string
     */
    protected $processorName;

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
     * Payment transaction processing errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Set payment transaction processor type
     *
     * @param       string      $processorType      Processor type
     *
     * @return      self
     *
     * @throws      RuntimeException                Unknown processor type given
     * @throws      RuntimeException                Processor type already specified
     */
    public function setProcessorType($processorType)
    {
        if (!in_array($processorType, static::$allowedProcessorTypes))
        {
            throw new RuntimeException("Unknown transaction processor type given: {$processorType}");
        }

        if (!empty($this->processorType))
        {
            throw new RuntimeException('You can set payment transaction processor type only once');
        }

        $this->processorType = $processorType;

        return $this;
    }

    /**
     * Get payment transaction processor type
     *
     * @return      string
     */
    public function getProcessorType()
    {
        return $this->processorType;
    }

    /**
     * Set payment transaction processor name
     *
     * @param       string      $processorName      Processor name
     *
     * @return      self
     *
     * @throws      RuntimeException                Processor name already specified
     */
    public function setProcessorName($processorName)
    {
        if (!empty($this->processorName))
        {
            throw new RuntimeException('You can set payment transaction processor name only once');
        }

        $this->processorName = $processorName;

        return $this;
    }

    /**
     * Get payment transaction processor name
     *
     * @return      string
     */
    public function getProcessorName()
    {
        return $this->processorName;
    }

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
     * True, if payment transaction declined or filtered
     *
     * @return      boolean
     */
    public function isDeclined()
    {
        return in_array($this->getStatus(), array(self::STATUS_FILTERED, self::STATUS_DECLINED));
    }

    /**
     * True, if error occurred when processing payment transaction by PaynetEasy gateway
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

        if (!$payment->hasPaymentTransaction($this))
        {
            $payment->addPaymentTransaction($this);
        }

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

    /**
     * Adds new payment error
     *
     * @param       Exception       $error      Payment error
     *
     * @return      self
     */
    public function addError(Exception $error)
    {
        // :NOTICE:         Imenem          19.06.13
        //
        // Use spl_object_hash to prevent duplicated errors
        $this->errors[spl_object_hash($error)] = $error;

        return $this;
    }

    /**
     * True if payment has errors
     *
     * @return      boolean
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * Get all payment errors
     *
     * @return      array       Payment errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get payment last error
     *
     * @return      Exception
     */
    public function getLastError()
    {
        if ($this->hasErrors())
        {
            $errors = $this->getErrors();
            return end($errors);
        }
    }
}
