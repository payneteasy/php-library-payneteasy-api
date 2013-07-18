<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Utils\String;
use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
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
     * [
     *     [<first field name>:string, <first property path>:string]
     *     [<second field name>:string, <second property path>:string]
     *     ...
     *     [<last field name>:string, <last property path>:string]
     * ]
     *
     * If property name present in field definition,
     * callback response field value and payment property value will be compared.
     * If values not equal validation exception will be throwned.
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
    final public function processCallback(Payment $payment, CallbackResponse $callbackResponse)
    {
        try
        {
            $this->validateCallback($payment, $callbackResponse);
        }
        catch (Exception $e)
        {
            $payment->addError($e)
                  ->setProcessingStage(Payment::STAGE_FINISHED)
                  ->setStatus(Payment::STATUS_ERROR);

            throw $e;
        }

        $this->updatePayment($payment, $callbackResponse);

        if ($callbackResponse->isError())
        {
            throw $callbackResponse->getError();
        }

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
     * @param       Payment               $payment                Payment
     * @param       CallbackResponse               $callbackResponse       Callback from paynet
     *
     * @throws      ValidationException                                    Validation error
     */
    protected function validateCallback(Payment $payment, CallbackResponse $callbackResponse)
    {
        $errorMessage   = '';
        $missedFields   = array();
        $unequalValues  = array();

        foreach (static::$callbackFieldsDefinition as $fieldDefinition)
        {
            list($fieldName, $propertyPath) = $fieldDefinition;

            if (empty($callbackResponse[$fieldName]))
            {
                $missedFields[] = $fieldName;
            }
            elseif ($propertyPath)
            {
                $propertyValue = PropertyAccessor::getValue($payment, $propertyPath, false);
                $callbackValue = $callbackResponse[$fieldName];

                if ($propertyValue != $callbackValue)
                {
                    $unequalValues[] = "CallbackResponse field '{$fieldName}' value '{$callbackValue}' does not " .
                                       "equal Payment property '{$propertyPath}' value '{$propertyValue}'";
                }
            }
        }

        if (!empty($missedFields))
        {
            $errorMessage .= "Some required fields missed or empty in CallbackResponse: \n" .
                             implode(', ', $missedFields) . ". \n";
        }

        if (!empty($unequalValues))
        {
            $errorMessage .= "Some fields from CallbackResponse unequal properties from Payment: \n" .
                             implode(", \n", $unequalValues) . ". \n";
        }

        if (!empty($errorMessage))
        {
            throw new ValidationException($errorMessage);
        }

        $this->validateControlCode($callbackResponse);

        if (!in_array($callbackResponse->getStatus(), static::$allowedStatuses))
        {
            throw new ValidationException("Invalid callback status: '{$callbackResponse->getStatus()}'. \n");
        }
    }

    /**
     * Updates Payment by Callback data
     *
     * @param       Payment        $payment        Payment for updating
     * @param       CallbackResponse        $response       Callback for payment updating
     */
    protected function updatePayment(Payment $payment, CallbackResponse $callbackResponse)
    {
        if($callbackResponse->isError())
        {
            $payment->setProcessingStage(Payment::STAGE_FINISHED);
            $payment->setStatus(Payment::STATUS_ERROR);
            $payment->addError($callbackResponse->getError());
        }
        elseif($callbackResponse->isApproved())
        {
            $payment->setProcessingStage(Payment::STAGE_FINISHED);
            $payment->setStatus(Payment::STATUS_APPROVED);
        }
        // "filtered" status is interpreted as the "DECLINED"
        elseif($callbackResponse->isDeclined())
        {
            $payment->setProcessingStage(Payment::STAGE_FINISHED);
            $payment->setStatus(Payment::STATUS_DECLINED);
            $payment->addError($callbackResponse->getError());
        }
        // If it does not redirect, it's processing
        elseif($callbackResponse->isProcessing())
        {
            $payment->setProcessingStage(Payment::STAGE_CREATED);
            $payment->setStatus(Payment::STATUS_PROCESSING);
        }

        $payment->setPaynetPaymentId($callbackResponse->getPaynetPaymentId());
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
            $callback->getPaynetPaymentId().
            $callback->getClientPaymentId().
            $this->config['control']
        );

        if($expectedControl !== $callback->getControlCode())
        {
            throw new ValidationException("Actual control code '{$callback->getControlCode()}' does " .
                                          "not equal expected '{$expectedControl}'");
        }
    }
}