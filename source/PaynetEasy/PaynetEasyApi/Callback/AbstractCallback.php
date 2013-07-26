<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use RuntimeException;
use Exception;

abstract class AbstractCallback implements CallbackInterface
{
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
     * @param       string      $callbackType       Callback type
     */
    public function __construct($callbackType)
    {
        $this->callbackType = $callbackType;
        $this->validateCallbackDefinition();
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
            $payment
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
     * Validates callback object definition
     *
     * @param       array       $config         API query object config
     *
     * @throws      RuntimeException
     */
    protected function validateCallbackDefinition()
    {
        if (empty(static::$allowedStatuses))
        {
            throw new RuntimeException('You must configure allowedStatuses property');
        }

        if (empty(static::$callbackFieldsDefinition))
        {
            throw new RuntimeException('You must configure callbackFieldsDefinition property');
        }
    }

    /**
     * Validates payment query config
     *
     * @param       Payment         $payment        Payment
     *
     * @throws      RuntimeException                Some query config property is empty
     */
    public function validateQueryConfig(Payment $payment)
    {
        $queryConfig = $payment->getQueryConfig();

        if(strlen($queryConfig->getSigningKey()) === 0)
        {
            throw new RuntimeException("Property 'signingKey' does not defined in Payment property 'queryConfig'");
        }
    }

    /**
     * Validates callback
     *
     * @param       Payment                 $payment                Payment
     * @param       CallbackResponse        $callbackResponse       Callback from paynet
     *
     * @throws      ValidationException                             Validation error
     */
    protected function validateCallback(Payment $payment, CallbackResponse $callbackResponse)
    {
        $this->validateQueryConfig($payment);
        $this->validateSignature($payment, $callbackResponse);

        if (!in_array($callbackResponse->getStatus(), static::$allowedStatuses))
        {
            throw new ValidationException("Invalid callback status: '{$callbackResponse->getStatus()}'");
        }

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
    protected function validateSignature(Payment $payment, CallbackResponse $callback)
    {
        // This is SHA-1 checksum of the concatenation
        // status + orderid + client_orderid + merchant-control.
        $expectedControl   = sha1
        (
            $callback->getStatus() .
            $callback->getPaynetPaymentId() .
            $callback->getClientPaymentId() .
            $payment->getQueryConfig()->getSigningKey()
        );

        if($expectedControl !== $callback->getControlCode())
        {
            throw new ValidationException("Actual control code '{$callback->getControlCode()}' does " .
                                          "not equal expected '{$expectedControl}'");
        }
    }
}