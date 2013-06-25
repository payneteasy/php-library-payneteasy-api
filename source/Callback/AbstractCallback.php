<?php

namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Utils\String;

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
     * Callback fields definition if format:
     * [<first field_name>:string, <second field_name>:string ... <last field_name>:string]
     *
     * @var array
     */
    static protected $callbackFieldsDefinition = array();

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
                  ->setTransportStage(OrderInterface::STAGE_ENDED)
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

        if (empty(static::$callbackFieldsDefinition))
        {
            throw new RuntimeException('You must configure callbackFieldsDefinition property');
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

        $this->callbackType = String::uncamelize($result[0]);
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
        $missedKeys = array();

        foreach (static::$callbackFieldsDefinition as $fieldName)
        {
            if (empty($callbackResponse[$fieldName]))
            {
                $missedKeys[] = $fieldName;
            }
        }

        if (!empty($missedKeys))
        {
            throw new ValidationException("Some required fields missed or empty in callback: " .
                                          implode(', ', $missedKeys));
        }

        $this->validateControlCode($callbackResponse);

        if (!in_array($callbackResponse->getStatus(), static::$allowedStatuses))
        {
            throw new ValidationException("Invalid callback status: {$callbackResponse->getStatus()}");
        }

        if ($callbackResponse->getClientOrderId() !== $order->getClientOrderId())
        {
            throw new ValidationException("Callback client_orderid '{$callbackResponse->getClientOrderId()}' does " .
                                          "not match Order client_orderid '{$order->getClientOrderId()}'");
        }

        if ($callbackResponse->getAmount() !== $order->getAmount())
        {
            throw new ValidationException("Callback amount '{$callbackResponse->getAmount()}' does " .
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
            $order->setTransportStage(OrderInterface::STAGE_ENDED);
            $order->setStatus(OrderInterface::STATUS_ERROR);
            $order->addError($callbackResponse->getError());
        }
        elseif($callbackResponse->isApproved())
        {
            $order->setTransportStage(OrderInterface::STAGE_ENDED);
            $order->setStatus(OrderInterface::STATUS_APPROVED);
        }
        // "filtered" status is interpreted as the "DECLINED"
        elseif($callbackResponse->isDeclined())
        {
            $order->setTransportStage(OrderInterface::STAGE_ENDED);
            $order->setStatus(OrderInterface::STATUS_DECLINED);
            $order->addError($callbackResponse->getError());
        }
        // If it does not redirect, it's processing
        elseif($callbackResponse->isProcessing())
        {
            $order->setTransportStage(OrderInterface::STAGE_CREATED);
            $order->setStatus(OrderInterface::STATUS_PROCESSING);
        }

        $order->setPaynetOrderId($callbackResponse->getPaynetOrderId());
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
            $callback->getStatus().
            $callback->getPaynetOrderId().
            $callback->getClientOrderId().
            $this->config['control']
        );

        if($expectedControl !== $callback->getControlCode())
        {
            throw new ValidationException("Actual control code '{$callback->getControlCode()}' does " .
                                          "not equal expected '{$expectedControl}'");
        }
    }
}