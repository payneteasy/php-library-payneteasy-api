<?php

namespace PaynetEasy\Paynet\OrderData;

use Exception;
use RuntimeException;

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
     * Order cancellation reason (up to 50 chars)
     *
     * @var string
     */
    protected $cancelReason;

    /**
     * Order processing errors
     *
     * @var array
     */
    protected $errors = array();

    public function __construct($array)
    {
        if(isset($array['order_code']))
        {
            $array['client_orderid']    = $array['order_code'];
            unset($array['order_code']);
        }

        if(isset($array['paynet_order_id']))
        {
            $array['orderid']    = $array['paynet_order_id'];
            unset($array['paynet_order_id']);
        }

        if(isset($array['desc']))
        {
            $array['order_desc']        = $array['desc'];
            unset($array['desc']);
        }

        $this->properties = array
        (
            'client_orderid'            => true,
            'order_desc'                => true,
            'amount'                    => true,
            'currency'                  => true,
            'ipaddress'                 => true,
            'site_url'                  => false,
            'orderid'                   => false
        );

        $this->validate_preg = array
        (
            'client_orderid'            => '|^[\S\s]{1,128}$|i',
            'order_desc'                => '|^[\S\s]{1,128}$|i',
            'amount'                    => '|^[0-9\.]{1,11}$|i',
            'currency'                  => '|^[A-Z]{1,3}$|i',
            'ipaddress'                 => '|^[0-9\.]{1,20}$|i',
            'site_url'                  => '|^[\S\s]{1,128}$|i',
            'orderid'                   => '|^[\S\s]{1,32}$|i'
        );

        parent::__construct($array);
    }

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
    public function createRecurrentCardFrom($cardRefId)
    {
        $this->setRecurrentCardFrom(new RecurrentCard($cardRefId));

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

    public function getOrderCode()
    {
        return $this->getValue('client_orderid');
    }

    public function getOrderId()
    {
        return $this->getOrderCode();
    }

    public function getPaynetOrderId()
    {
        return $this->getValue('orderid');
    }

    public function setPaynetOrderId($paynet_order_id)
    {
        $this->offsetSet('orderid', $paynet_order_id);
    }

    public function getAmount()
    {
        return $this->getValue('amount');
    }

    /**
     * Return amount in cents (use for control code)
     * @return type
     */
    public function getAmountInCents()
    {
        $amount         = (float)$this->getValue('amount');
        $amount         = explode('.', $amount);
        if(empty($amount[1]))
        {
            $amount[1]  = '00';
        }
        elseif(strlen($amount[1]) < 2)
        {
            $amount[1]  .= '0';
        }

        if(empty($amount[0]))
        {
            $amount[0]  = '';
        }

        return          $amount[0].$amount[1];
    }

    public function getCurrency()
    {
        return $this->getValue('currency');
    }

    public function getDesc()
    {
        return $this->getValue('order_desc');
    }

    public function getContextData()
    {
        return array
        (
            'client_orderid'    => $this->getOrderCode(),
            'orderid'           => $this->getPaynetOrderId()
        );
    }

    public function validateShort()
    {
        if(!$this->getPaynetOrderId())
        {
            throw new RuntimeException('order.paynet_order_id undefined');
        }

        if(!$this->getOrderCode())
        {
            throw new RuntimeException('order.order_code undefined');
        }
    }
}