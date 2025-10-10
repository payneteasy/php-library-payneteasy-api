<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\Util\PropertyAccessor;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
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
    static protected $allowedStatuses = array
    (
        PaymentTransaction::STATUS_APPROVED,
        PaymentTransaction::STATUS_DECLINED,
        PaymentTransaction::STATUS_FILTERED,
        PaymentTransaction::STATUS_ERROR
    );

    /**
     * Callback fields definition if format:
     * [
     *     [<first callback field name>:string, <first payment transaction property path>:string]
     *     [<second callback field name>:string, <second payment transaction property path>:string]
     *     ...
     *     [<last callback field name>:string, <last payment transaction property path>:string]
     * ]
     *
     * If property name present in field definition,
     * callback response field value and payment transaction property value will be compared.
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
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        try
        {
            $this->validateCallback($paymentTransaction, $callbackResponse);
        }
        catch (Exception $e)
        {
            $paymentTransaction
                ->addError($e)
                ->setStatus(PaymentTransaction::STATUS_ERROR)
            ;

            throw $e;
        }

        $this->updatePaymentTransaction($paymentTransaction, $callbackResponse);

        if ($callbackResponse->isError())
        {
            throw $callbackResponse->getError();
        }

        return $callbackResponse;
    }

    /**
     * Validates callback object definition
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
     * Validates payment transaction query config
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     *
     * @throws      RuntimeException                                Some query config property is empty
     */
    public function validateQueryConfig(PaymentTransaction $paymentTransaction)
    {
        if(strlen($paymentTransaction->getQueryConfig()->getSigningKey()) === 0)
        {
            throw new ValidationException("Property 'signingKey' does not defined in PaymentTransaction property 'queryConfig'");
        }
    }

    /**
     * Validates callback
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       CallbackResponse        $callbackResponse       Callback from PaynetEasy
     *
     * @throws      ValidationException                             Validation error
     */
    protected function validateCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        $this->validateQueryConfig($paymentTransaction);
        $this->validateSignature($paymentTransaction, $callbackResponse);

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
                $propertyValue = PropertyAccessor::getValue($paymentTransaction, $propertyPath, false);
                $callbackValue = $callbackResponse[$fieldName];

                if ($propertyValue != $callbackValue)
                {
                    $unequalValues[] = "CallbackResponse field '{$fieldName}' value '{$callbackValue}' does not " .
                                       "equal PaymentTransaction property '{$propertyPath}' value '{$propertyValue}'";
                }
            }
        }

        if (!empty($missedFields))
        {
            $errorMessage .= "Some required fields missed or empty in CallbackResponse: \n" .
                             implode(', ', $missedFields) . ".\n";
        }

        if (!empty($unequalValues))
        {
            $errorMessage .= "Some fields from CallbackResponse unequal properties from PaymentTransaction: \n" .
                             implode("\n", $unequalValues) . " \n";
        }

        if (!empty($errorMessage))
        {
            throw new ValidationException($errorMessage);
        }
    }

    /**
     * Updates Payment transaction by Callback data
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for updating
     * @param       CallbackResponse        $response               Callback for payment transaction updating
     */
    protected function updatePaymentTransaction(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        $paymentTransaction->setStatus($callbackResponse->getStatus());
        $paymentTransaction->getPayment()->setPaynetId($callbackResponse->getPaymentPaynetId());

        if ($callbackResponse->isError() || $callbackResponse->isDeclined())
        {
            $paymentTransaction->addError($callbackResponse->getError());
        }
    }

    /**
     * Validate callback response control code
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for control code checking
     * @param       CallbackResponse        $callbackResponse       Callback for control code checking
     *
     * @throws      ValidationException                             Invalid control code
     */
    protected function validateSignature(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        // This is SHA-1 checksum of the concatenation
        // status + orderid + client_orderid + merchant-control.
        $expectedControlCode = sha1
        (
            $callbackResponse->getStatus() .
            $callbackResponse->getPaymentPaynetId() .
            $callbackResponse->getPaymentClientId() .
            $paymentTransaction->getQueryConfig()->getSigningKey()
        );

        if($expectedControlCode !== $callbackResponse->getControlCode())
        {
            throw new ValidationException("Actual control code '{$callbackResponse->getControlCode()}' does " .
                                          "not equal expected '{$expectedControlCode}'");
        }
    }
}