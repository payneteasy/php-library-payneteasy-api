<?php

namespace PaynetEasy\Paynet\OrderData;

use RuntimeException;
use Exception;

/**
 * Container for order data
 *
 */
class       Order
extends     Data
implements  OrderInterface
{
    /**
     * All allowed order states
     *
     * @var array
     */
    /**
     * @todo More specific name and description needed
     */
    static protected $allowedStates = array
    (
        self::STATE_NULL,
        self::STATE_INIT,
        self::STATE_REDIRECT,
        self::STATE_PROCESSING,
        self::STATE_END
    );

    /**
     * All allowed order statuses
     *
     * @var array
     */
    /**
     * @todo More specific name and description needed
     */
    static protected $allowedStatuses = array
    (
        self::STATE_PROCESSING,
        self::STATUS_APPROVED,
        self::STATUS_DECLINED,
        self::STATUS_ERROR
    );

    /**
     * Merchant order identifier
     *
     * @var string
     */
    protected $clientOrderId;

    /**
     * Unique identifier of transaction assigned by PaynetEasy
     *
     * @var string
     */
    protected $paynetOrderId;

    /**
     * Brief order description
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
     * Order state
     *
     * @var string
     */
    /**
     * @todo More specific name and description needed
     */
    protected $state = self::STATE_NULL;

    /**
     * Order status
     *
     * @var string
     */
    /**
     * @todo More specific name and description needed
     */
    protected $status = '';

    /**
     * Order customer
     *
     * @var \PaynetEasy\Paynet\OrderData\CustomerInterface
     */
    protected $customer;

    /**
     * Order credit card
     *
     * @var \PaynetEasy\Paynet\OrderData\CreditCardInterface
     */
    protected $creditCard;

    /**
     * Order source recurrent card
     *
     * @var \PaynetEasy\Paynet\OrderData\RecurrentCardInterface
     */
    protected $recurrentCardFrom;

    /**
     * Order destination recurrent card
     *
     * @var \PaynetEasy\Paynet\OrderData\RecurrentCardInterface
     */
    protected $recurrentCardTo;

    /**
     * Order processing errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * {@inheritdoc}
     */
    public static function getAllowedStates()
    {
        return static::$allowedStates;
    }

    /**
     * {@inheritdoc}
     */
    public static function getAllowedStatuses()
    {
        return static::$allowedStatuses;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientOrderId($clientOrderId)
    {
        $this->clientOrderId = $clientOrderId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOrderId()
    {
        return $this->clientOrderId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaynetOrderId($paynetOrderId)
    {
        $this->paynetOrderId = $paynetOrderId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaynetOrderId()
    {
        return $this->paynetOrderId;
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
    public function createRecurrentCardFrom($cardReferenceId)
    {
        $this->setRecurrentCardFrom(new RecurrentCard(array('cardrefid' => $cardReferenceId)));

        return $this;
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
    public function setState($state)
    {
        if (!in_array($state, static::getAllowedStates()))
        {
            throw new RuntimeException("Unknown state given: {$state}");
        }

        $this->state = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        if (!in_array($status, static::getAllowedStatuses()))
        {
            throw new RuntimeException("Unknown state given: {$status}");
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
            {
                return 'setClientOrderId';
            }
            case 'paynet_order_id':
            case 'paynet-order-id':
            case 'orderid':
            case 'order_id':
            case 'order-id':
            {
                return 'setPaynetOrderId';
            }
            case 'order_desc':
            case 'order-desc':
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