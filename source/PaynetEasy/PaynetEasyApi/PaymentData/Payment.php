<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

use RuntimeException;
use Exception;

/**
 * Container for payment data
 *
 */
class       Payment
extends     Data
implements  PaymentInterface
{
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
     * Customer’s IP address
     *
     * @var string
     */
    protected $ipAddress;

    /**
     * URL the original payment is made from
     *
     * @var string
     */
    protected $siteUrl;

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
     * Payment customer
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\CustomerInterface
     */
    protected $customer;

    /**
     * Payment credit card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\CreditCardInterface
     */
    protected $creditCard;

    /**
     * Payment source recurrent card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCardInterface
     */
    protected $recurrentCardFrom;

    /**
     * Payment destination recurrent card
     *
     * @var \PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCardInterface
     */
    protected $recurrentCardTo;

    /**
     * Payment processing errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * {@inheritdoc}
     */
    public function setClientPaymentId($clientPaymentId)
    {
        $this->clientPaymentId = $clientPaymentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientPaymentId()
    {
        return $this->clientPaymentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaynetPaymentId($paynetPaymentId)
    {
        $this->paynetPaymentId = $paynetPaymentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaynetPaymentId()
    {
        return $this->paynetPaymentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountInCents()
    {
        return (int) ($this->getAmount() * 100);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreditCard(CreditCardInterface $creditCard)
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecurrentCardFrom(RecurrentCardInterface $recurrentCard)
    {
        $this->recurrentCardFrom = $recurrentCard;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrentCardFrom()
    {
        return $this->recurrentCardFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecurrentCardTo(RecurrentCardInterface $recurrentCard)
    {
        $this->recurrentCardTo = $recurrentCard;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrentCardTo()
    {
        return $this->recurrentCardTo;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getProcessingStage()
    {
        return $this->processingStage;
    }

    /**
     * {@inheritdoc}
     */
    public function isCreated()
    {
        return $this->getProcessingStage() == self::STAGE_CREATED;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirected()
    {
        return $this->getProcessingStage() == self::STAGE_REDIRECTED;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinished()
    {
        return $this->getProcessingStage() == self::STAGE_FINISHED;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isProcessing()
    {
        return $this->getStatus() == self::STATUS_PROCESSING;
    }

    /**
     * {@inheritdoc}
     */
    public function isApproved()
    {
        return $this->getStatus() == self::STATUS_APPROVED;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeclined()
    {
        return $this->getStatus() == self::STATUS_DECLINED;
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return $this->getStatus() == self::STATUS_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
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
    protected function getSetterByField($fieldName)
    {
        switch ($fieldName)
        {
            case 'client_orderid':
            case 'client-orderid':
            case 'client_order_id':
            case 'client-order-id':
            case 'client_payment_id':
            case 'client-payment-id':
            {
                return 'setClientPaymentId';
            }
            case 'paynet_order_id':
            case 'paynet-order-id':
            case 'paynet_payment_id':
            case 'paynet-payment-id':
            case 'orderid':
            case 'order_id':
            case 'order-id':
            {
                return 'setPaynetPaymentId';
            }
            case 'order_desc':
            case 'order-desc':
            case 'payment_desc':
            case 'payment-desc':
            case 'desc':
            case 'description':
            {
                return 'setDescription';
            }
            case 'ipaddress':
            case 'ip_address':
            case 'ip-address':
            {
                return 'setIpAddress';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }
}