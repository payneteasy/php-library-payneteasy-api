<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\CallbackResponse;

use PaynetEasy\Paynet\Exception\ValidationException;
use RuntimeException;
use Exception;

abstract class AbstractCallback implements CallbackInterface
{
    /**
     * Config for Paynet callback
     *
     * @var array
     */
    protected $config;

    /**
     * Callback type
     *
     * @var string
     */
    protected $callbackType;

    /**
     * Allowed callback statuses
     *
     * @var array
     */
    static protected $allowedStatuses = array();

    /**
     * Allowed callback fields
     * in format [<field name>:string => <is field required>:boolean]
     *
     * @var array
     */
    static protected $allowedFields = array();

    /**
     * @param       array       $config         Paynet callback object config
     */
    public function __construct(array $config = array())
    {
        $this->setCallbackType(get_called_class());
        $this->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    final public function processCallback(OrderInterface $order, CallbackResponse $callbackResponse)
    {
        try
        {
            $this->validateCallback($order, $callbackResponse);
        }
        catch (Exception $e)
        {
            $order->addError($e)
                  ->setState(OrderInterface::STATE_END)
                  ->setStatus(OrderInterface::STATUS_ERROR);

            throw $e;
        }

        $this->updateOrder($order, $callbackResponse);

        return $callbackResponse;
    }

    /**
     * Set query object config
     *
     * @param       array       $config         API query object config
     *
     * @throws      RuntimeException
     */
    protected function setConfig(array $config)
    {
        if (empty(static::$allowedStatuses))
        {
            throw new RuntimeException('You must configure allowedStatuses property');
        }

        if (empty(static::$allowedFields))
        {
            throw new RuntimeException('You must configure allowedFields property');
        }

        if(empty($config['control']))
        {
            throw new RuntimeException("You must set 'control' field in config");
        }

        $this->config = $config;
    }

    /**
     * Set callback type. Callback class name must follow next convention:
     *
     * (callback type)              (callback class name)
     * redirect_url         =>      RedirectUrlCallback
     * server_callback_url  =>      ServerCallbackUrlCallback
     *
     * @param       string      $class          Callback object class
     */
    protected function setCallbackType($class)
    {
        if (!empty($this->callbackType))
        {
            return;
        }

        $result = array();

        preg_match('#(?<=\\\\)\w+(?=Callback)#i', $class, $result);

        if (empty($result))
        {
            throw new RuntimeException('Callback type not found in class name');
        }

        $nameChunks     = preg_split('/(?=[A-Z])/', $result[0], null, PREG_SPLIT_NO_EMPTY);
        $this->callbackType = strtolower(implode('-', $nameChunks));
    }

    /**
     * Validates callback
     *
     * @param       OrderInterface                 $order                  Order
     * @param       CallbackResponse               $callbackResponse       Callback from paynet
     *
     * @throws      ValidationException                                    Validation error
     */
    protected function validateCallback(OrderInterface $order, CallbackResponse $callbackResponse)
    {
        $this->validateControlCode($callbackResponse);

        $missedKeys = array();

        foreach (static::$allowedFields as $fieldName => $isFieldRequired)
        {
            if ($isFieldRequired && empty($callbackResponse[$fieldName]))
            {
                $missedKeys[] = $fieldName;
            }
        }

        if (!empty($missedKeys))
        {
            throw new ValidationException("Some required fields missed or empty in callback: " .
                                          implode(', ', $missedKeys));
        }

        if (!in_array($callbackResponse->status(), static::$allowedStatuses))
        {
            throw new ValidationException("Invalid callback status: {$callbackResponse->status()}");
        }

        if ($callbackResponse->orderId() !== $order->getOrderId())
        {
            throw new ValidationException("Callback client_orderid '{$callbackResponse->orderId()}' does " .
                                          "not match Order client_orderid '{$order->getOrderId()}'");
        }

        if ($callbackResponse->amount() !== $order->getAmount())
        {
            throw new ValidationException("Callback amount '{$callbackResponse->amount()}' does " .
                                          "not match Order amount '{$order->getAmount()}'");
        }
    }

    /**
     * Updates Order by Callback data
     *
     * @param       OrderInterface         $order          Order for updating
     * @param       CallbackResponse       $response       Callback for order updating
     */
    protected function updateOrder(OrderInterface $order, CallbackResponse $callbackResponse)
    {
        if($callbackResponse->isError())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_ERROR);
            $order->addError($callbackResponse->error());
        }
        elseif($callbackResponse->isApproved())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_APPROVED);
        }
        // "filtered" status is interpreted as the "DECLINED"
        elseif($callbackResponse->isDeclined())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_DECLINED);
        }
        // If it does not redirect, it's processing
        elseif($callbackResponse->isProcessing())
        {
            $order->setState(OrderInterface::STATE_PROCESSING);
            $order->setStatus(OrderInterface::STATUS_PROCESSING);
        }

        $order->setPaynetOrderId($callbackResponse->paynetOrderId());
    }

    /**
     * Validate callback response control code
     *
     * @param       CallbackResponse        $callback       Callback for control code checking
     *
     * @throws      ValidationException                     Invalid control code
     */
    protected function validateControlCode(CallbackResponse $callback)
    {
        // This is SHA-1 checksum of the concatenation
        // status + orderid + client_orderid + merchant-control.
        $expectedControl   = sha1
        (
            $callback->status().
            $callback->paynetOrderId().
            $callback->orderId().
            $this->config['control']
        );

        if($expectedControl !== $callback->control())
        {
            throw new ValidationException("Actual control code '{$callback->control()}' does " .
                                          "not equal expected '{$expectedControl}'");
        }
    }
}