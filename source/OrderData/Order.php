<?php

namespace PaynetEasy\Paynet\OrderData;

use PaynetEasy\Paynet\Exception\ValidationException;
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
     * Order cancellation reason (up to 50 chars)
     *
     * @var string
     */
    protected $cancelReason;

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
        $this->validateValue($clientOrderId, '#^[\S\s]{1,128}$#i');

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
        $this->validateValue($paynetOrderId, '#^[\S\s]{1,20}$#i');

        $this->paynetOrderId = $paynetOrderId;

        return $this;
    }

    /**
     * Get unique identifier of transaction assigned by PaynetEasy
     *
     * @return       string
     */
    public function getPaynetOrderId()
    {
        return $this->paynetOrderId;
    }

    /**
     * Set brief order description
     *
     * @param       string      $description        Brief order description
     */
    public function setDescription($description)
    {
        $this->validateValue($description, '#^[\S\s]{1,125}$#i');

        $this->description = $description;

        return $this;
    }

    /**
     * Get brief order description
     *
     * @return      string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get amount to be charged
     *
     * @param       float       $amount             Amount to be charged
     */
    public function setAmount($amount)
    {
        $this->validateValue($amount, '#^[0-9\.]{1,11}$#i');

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
     * @return integer
     */
    public function getAmountInCents()
    {
        return (int) ($this->getAmount() * 100);
    }

    /**
     * Set currency the transaction is charged in (three-letter currency code)
     *
     * @param       string      $currency           Currency the transaction is charged in
     */
    public function setCurrency($currency)
    {
        $this->validateValue($currency, '#^[A-Z]{1,3}$#i');

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
     * Set customer’s IP address
     *
     * @param       string      $ipAddress          Customer’s IP address
     */
    public function setIpAddress($ipAddress)
    {
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP))
        {
            throw new ValidationException("Invalid IP address '{$ipAddress}'");
        }

        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get customer’s IP address
     *
     * @return      string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set URL the original payment is made from
     *
     * @param       string      $siteUrl            URL the original payment is made from
     */
    public function setSiteUrl($siteUrl)
    {
        if (!filter_var($siteUrl, FILTER_VALIDATE_URL))
        {
            throw new ValidationException("Invalid site URL '{$siteUrl}'");
        }

        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * Get URL the original payment is made from
     *
     * @return      string
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
    public function hasCustomer()
    {
        return is_object($this->getCustomer());
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
    public function hasCreditCard()
    {
        return is_object($this->getCreditCard());
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
    public function hasRecurrentCardFrom()
    {
        return is_object($this->getRecurrentCardFrom());
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
    public function hasRecurrentCardTo()
    {
        return is_object($this->getRecurrentCardTo());
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
    public function setCancelReason($cancelReason)
    {
        if(strlen($cancelReason) > 50)
        {
            throw new RuntimeException('Cancellation reason is very long (over 50 characters)');
        }

        $this->cancelReason = $cancelReason;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelReason()
    {
        return $this->cancelReason;
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
     * @todo Move to AbstractQuery
     */
    public function getContextData()
    {
        return array
        (
            'client_orderid'    => $this->getClientOrderId(),
            'orderid'           => $this->getPaynetOrderId()
        );
    }

    /**
     * @todo Move to AbstractQuery
     */
    public function validateShort()
    {
        if(!$this->getPaynetOrderId())
        {
            throw new RuntimeException('order.paynet_order_id undefined');
        }

        if(!$this->getClientOrderId())
        {
            throw new RuntimeException('order.client_orderid undefined');
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
            case 'client_order_id':
            {
                return 'setClientOrderId';
            }
            case 'paynet_order_id':
            case 'orderid':
            case 'order_id':
            {
                return 'setPaynetOrderId';
            }
            case 'order_desc':
            {
                return 'setDescription';
            }
            case 'ipaddress':
            {
                return 'setIpAddress';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldByProperty($propertyName)
    {
        switch ($propertyName)
        {
            case 'clientOrderId':
            {
                return 'client_orderid';
            }
            case 'paynetOrderId':
            {
                return 'orderid';
            }
            case 'description':
            {
                return 'order_desc';
            }
            case 'ipAddress':
            {
                return 'ipaddress';
            }
            default:
            {
                return parent::getFieldByProperty($propertyName);
            }
        }
    }
}