<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use RuntimeException;
use Exception;

/**
 * Container for payment data
 */
class Payment extends Data
{
    /**
     * Payment created in bank
     */
    const STAGE_CREATED     = 'created';

    /**
     * Customer is redirected to Paynet to perform additional steps
     */
    const STAGE_REDIRECTED  = 'redirected';

    /**
     * Payment processing is ended
     */
    const STAGE_FINISHED    = 'ended';

    /**
     * Payment is now processing
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * Payment approved
     */
    const STATUS_APPROVED   = 'approved';

    /**
     * Payment declined by bank
     */
    const STATUS_DECLINED   = 'declined';

    /**
     * Payment declined by Paynet filters
     */
    const STATUS_FILTERED   = 'filtered';

    /**
     * Payment processed with error
     */
    const STATUS_ERROR      = 'error';

    /**
     * All allowed payment processing stages
     *
     * @var array
     */
    static protected $allowedProcessingStages = array
    (
        self::STAGE_CREATED,
        self::STAGE_REDIRECTED,
        self::STAGE_FINISHED
    );

    /**
     * All allowed payment statuses in bank
     *
     * @var array
     */
    static protected $allowedStatuses = array
    (
        self::STATUS_PROCESSING,
        self::STATUS_APPROVED,
        self::STATUS_DECLINED,
        self::STATUS_ERROR
    );

    /**
     * Merchant payment identifier
     *
     * @var string
     */
    protected $clientPaymentId;

    /**
     * Unique identifier of transaction assigned by PaynetEasy
     *
     * @var string
     */
    protected $paynetPaymentId;

    /**
     * Brief payment description
     *
     * @var string
     */
    protected $description;

    /**
     * Destination to where the payment goes
     *
     * @var string
     */
    protected $destination;

    /**
     * Amount to be charged
     *
     * @var float
     */
    protected $amount;

    /**
     * Currency the transaction is charged in (three-letter currency code)
     *
     * @var string
     */
    protected $currency;

    /**
     * A short comment for payment
     *
     * @var string
     */
    protected $comment;

    /**
     * Payment processing stage
     *
     * @var string
     */
    protected $processingStage;

    /**
     * Payment status in bank
     *
     * @var string
     */
    protected $status;

    /**
     * Payment query config
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig
     */
    protected $queryConfig;

    /**
     * Payment customer
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\Customer
     */
    protected $customer;

    /**
     * Payment billing address
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress
     */
    protected $billingAddress;

    /**
     * Payment credit card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\CreditCard
     */
    protected $creditCard;

    /**
     * Payment source recurrent card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
     */
    protected $recurrentCardFrom;

    /**
     * Payment destination recurrent card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard
     */
    protected $recurrentCardTo;

    /**
     * Payment processing errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Set merchant payment identifier
     *
     * @param       string      $clientPaymentId        Merchant payment identifier
     *
     * @return      self
     */
    public function setClientPaymentId($clientPaymentId)
    {
        $this->clientPaymentId = $clientPaymentId;

        return $this;
    }

    /**
     * Get merchant payment identifier
     *
     * @return      string
     */
    public function getClientPaymentId()
    {
        return $this->clientPaymentId;
    }

    /**
     * Set unique identifier of transaction assigned by PaynetEasy
     *
     * @param       string      $paynetPaymentId        Unique identifier of transaction assigned by PaynetEasy
     *
     * @return      self
     */
    public function setPaynetPaymentId($paynetPaymentId)
    {
        $this->paynetPaymentId = $paynetPaymentId;

        return $this;
    }

    /**
     * Get unique identifier of transaction assigned by PaynetEasy
     *
     * @return       string
     */
    public function getPaynetPaymentId()
    {
        return $this->paynetPaymentId;
    }

    /**
     * Set brief payment description
     *
     * @param       string      $description        Brief payment description
     *
     * @return      self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get brief payment description
     *
     * @return      string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set destination to where the payment goes
     *
     * @param       string      $destination        Destination to where the payment goes
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination to where the payment goes
     *
     * @return      string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get amount to be charged
     *
     * @param       float       $amount             Amount to be charged
     *
     * @return      self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount to be charged
     *
     * @return      float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get amount in cents (for control code generation)
     *
     * @return      integer
     */
    public function getAmountInCents()
    {
        return (int) ($this->getAmount() * 100);
    }

    /**
     * Set currency the transaction is charged in (three-letter currency code)
     *
     * @param       string      $currency           Currency the transaction is charged in
     *
     * @return      self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency the transaction is charged in (three-letter currency code)
     *
     * @return      string
     */
    public function getCurrency()
    {
        return $this->currency;
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
     * Set payment customer
     *
     * @param       Customer       $customer           Payment customer
     *
     * @return      self
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get payment customer
     *
     * @return      Customer
     */
    public function getCustomer()
    {
        if (empty($this->customer))
        {
            $this->customer = new Customer;
        }

        return $this->customer;
    }

    /**
     * Set payment billing address
     *
     * @param       BillingAddress      $billingAddress     Billing address
     *
     * @return      self
     */
    public function setBillingAddress(BillingAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get payment billing address
     *
     * @return      BillingAddress
     */
    public function getBillingAddress()
    {
        if (empty($this->billingAddress))
        {
            $this->billingAddress = new BillingAddress;
        }

        return $this->billingAddress;
    }

    /**
     * Set payment credit card
     *
     * @param       CreditCard     $creditCard         Payment credit card
     *
     * @return      self
     */
    public function setCreditCard(CreditCard $creditCard)
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * Get credit card
     *
     * @return      CreditCard
     */
    public function getCreditCard()
    {
        if (empty($this->creditCard))
        {
            $this->creditCard = new CreditCard;
        }

        return $this->creditCard;
    }

    /**
     * Set payment sorce recurrent card
     *
     * @param       RecurrentCard      $recurrentCard      Source recurrent card
     *
     * @return      self
     */
    public function setRecurrentCardFrom(RecurrentCard $recurrentCard)
    {
        $this->recurrentCardFrom = $recurrentCard;

        return $this;
    }

    /**
     * Get payment source recurrent card
     *
     * @return      RecurrentCard
     */
    public function getRecurrentCardFrom()
    {
        if (empty($this->recurrentCardFrom))
        {
            $this->recurrentCardFrom = new RecurrentCard;
        }

        return $this->recurrentCardFrom;
    }

    /**
     * Set payment destination recurrent card
     *
     * @param       RecurrentCard      $recurrentCard      Destination recurrent card
     *
     * @return      self
     */
    public function setRecurrentCardTo(RecurrentCard $recurrentCard)
    {
        $this->recurrentCardTo = $recurrentCard;

        return $this;
    }

    /**
     * Get payment destination recurrent card
     *
     * @return      RecurrentCard
     */
    public function getRecurrentCardTo()
    {
        if (empty($this->recurrentCardTo))
        {
            $this->recurrentCardTo = new RecurrentCard;
        }

        return $this->recurrentCardTo;
    }

    /**
     * Set payment processing stage
     *
     * @param       string      $processingStage      Payment transport stage
     *
     * @return      self
     */
    public function setProcessingStage($processingStage)
    {
        if (!in_array($processingStage, static::$allowedProcessingStages))
        {
            throw new RuntimeException("Unknown transport stage given: '{$processingStage}'");
        }

        $this->processingStage = $processingStage;

        return $this;
    }

    /**
     * Get payment processing state
     *
     * @return      string
     */
    public function getProcessingStage()
    {
        return $this->processingStage;
    }

    /**
     * True, if payment created in bank
     *
     * @return      boolean
     */
    public function isCreated()
    {
        return $this->getProcessingStage() == self::STAGE_CREATED;
    }

    /**
     * True, if customer is redirected to Paynet to perform additional steps
     *
     * @return      boolean
     */
    public function isRedirected()
    {
        return $this->getProcessingStage() == self::STAGE_REDIRECTED;
    }

    /**
     * True, if transport stage is finished
     *
     * @return      boolean
     */
    public function isFinished()
    {
        return $this->getProcessingStage() == self::STAGE_FINISHED;
    }

    /**
     * Set payment bank status
     *
     * @param       string      $status     Payment bank status
     *
     * @return      self
     */
    public function setStatus($status)
    {
        if (!in_array($status, static::$allowedStatuses))
        {
            throw new RuntimeException("Unknown bank status given: {$status}");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get payment bank status
     *
     * @return      string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * True, if payment is now processing
     *
     * @return      boolean
     */
    public function isProcessing()
    {
        return $this->getStatus() == self::STATUS_PROCESSING;
    }

    /**
     * True, if payment approved
     *
     * @return      boolean
     */
    public function isApproved()
    {
        return $this->getStatus() == self::STATUS_APPROVED;
    }

    /**
     * True, if payment declined
     *
     * @return      boolean
     */
    public function isDeclined()
    {
        return $this->getStatus() == self::STATUS_DECLINED;
    }

    /**
     * True, if error occured when processing payment
     *
     * @return      boolean
     */
    public function isError()
    {
        return $this->getStatus() == self::STATUS_ERROR;
    }

    /**
     * Set payment short comment
     *
     * @param       string      $comment    A short comment
     *
     * @return      self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get payment short comment
     *
     * @return      string      A short comment
     */
    public function getComment()
    {
        return $this->comment;
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

    /**
     * {@inheritdoc}
     */
    protected function getPropertyByField($fieldName)
    {
        switch ($fieldName)
        {
            case 'client_orderid':
            case 'client-orderid':
            {
                return 'ClientPaymentId';
            }
            case 'paynet_order_id':
            case 'paynet-order-id':
            case 'orderid':
            {
                return 'PaynetPaymentId';
            }
            case 'order_desc':
            case 'order-desc':
            {
                return 'Description';
            }
            case 'ipaddress':
            {
                return 'IpAddress';
            }
            default:
            {
                return parent::getPropertyByField($fieldName);
            }
        }
    }
}