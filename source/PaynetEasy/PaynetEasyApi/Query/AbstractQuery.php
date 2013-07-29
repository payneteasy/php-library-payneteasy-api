<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\Utils\Validator;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\Request;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use RuntimeException;
use Exception;

/**
 * Abstract Query
 */
abstract class  AbstractQuery
implements      QueryInterface
{
    /**
     * API gateway method
     *
     * @var string
     */
    protected $apiMethod;

    /**
     * Request fields definition in format
     * [
     *     [<first field name>:string,  <first payment property path>:string,   <is field required>:boolean, <validation rule>:string],
     *     [<second field name>:string, <second payment property path>:string,  <is field required>:boolean, <validation rule>:string],
     *     ...
     *     [<last field name>:string,   <last payment property path>:string,    <is field required>:boolean, <validation rule>:string]
     * ]
     *
     * @var array
     */
    static protected $requestFieldsDefinition = array();

    /**
     * Request control code definition in format
     * [<first part property path>:string, <second part property path>:string ... <last part property path>:string]
     *
     * @var array
     */
    static protected $signatureDefinition = array();

    /**
     * Response fields definition in format:
     * [<first field_name>:string, <second field_name>:string ... <last field_name>:string]
     *
     * @var array
     */
    static protected $responseFieldsDefinition = array();

    /**
     * Success response type
     *
     * @var string
     */
    static protected $successResponseType;

    /**
     * @param       string      $apiMethod      API gateway method
     */
    public function __construct($apiMethod)
    {
        $this->apiMethod = $apiMethod;
        $this->validateQueryDefinition();
    }

    /**
     * {@inheritdoc}
     */
    final public function createRequest(PaymentTransaction $paymentTransaction)
    {
        try
        {
            $this->validatePaymentTransaction($paymentTransaction);
        }
        catch (Exception $e)
        {
            $paymentTransaction
                  ->setProcessingStage(PaymentTransaction::STAGE_FINISHED)
                  ->setStatus(PaymentTransaction::STATUS_ERROR);

            throw $e;
        }

        $request = new Request($this->paymentTransactionToRequest($paymentTransaction));

        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($paymentTransaction->getQueryConfig()->getEndPoint())
                ->setGatewayUrl($paymentTransaction->getQueryConfig()->getGatewayUrl())
                ->setSignature($this->createSignature($paymentTransaction));

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    final public function processResponse(PaymentTransaction $paymentTransaction, Response $response)
    {
        if(   !$response->isProcessing()
           && !$response->isApproved())
        {
            $validate = array($this, 'validateResponseOnError');
            $update   = array($this, 'updatePaymentTransactionOnError');
        }
        else
        {
            $validate = array($this, 'validateResponseOnSuccess');
            $update   = array($this, 'updatePaymentTransactionOnSuccess');
        }

        try
        {
            call_user_func($validate, $paymentTransaction, $response);
        }
        catch (Exception $e)
        {
            $paymentTransaction
                  ->setProcessingStage(PaymentTransaction::STAGE_FINISHED)
                  ->setStatus(PaymentTransaction::STATUS_ERROR);

            throw $e;
        }

        call_user_func($update, $paymentTransaction, $response);

        if ($response->isError())
        {
            throw $response->getError();
        }

        return $response;
    }

    /**
     * Validates payment transaction before query constructing
     *
     * @param       PaymentTransaction      $paymentTransaction        Payment transaction for validation
     */
    protected function validatePaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        $this->validateQueryConfig($paymentTransaction);

        $errorMessage   = '';
        $missedFields   = array();
        $invalidFields  = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath, $isFieldRequired, $validationRule) = $fieldDescription;

            $fieldValue = PropertyAccessor::getValue($paymentTransaction, $propertyPath, false);

            if (!empty($fieldValue))
            {
                try
                {
                    Validator::validateByRule($fieldValue, $validationRule);
                }
                catch (ValidationException $e)
                {
                    $invalidFields[] = "Field '{$fieldName}', {$e->getMessage()}";
                }
            }
            elseif ($isFieldRequired)
            {
                $missedFields[] = $fieldName;
            }
        }

        if (!empty($missedFields))
        {
            $errorMessage .= "Some required fields missed or empty in Payment: \n" .
                             implode(', ', $missedFields) . ". \n";
        }

        if (!empty($invalidFields))
        {
            $errorMessage .= "Some fields invalid in Payment: \n" .
                             implode(", \n", $invalidFields) . ". \n";
        }

        if (!empty($errorMessage))
        {
            throw new ValidationException($errorMessage);
        }
    }

    /**
     * Creates request from payment transaction
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment for request
     *
     * @return      array                                           Request
     */
    protected function paymentTransactionToRequest(PaymentTransaction $paymentTransaction)
    {
        $request = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath) = $fieldDescription;

            $fieldValue = PropertyAccessor::getValue($paymentTransaction, $propertyPath);

            if (!empty($fieldValue))
            {
                $request[$fieldName] = $fieldValue;
            }
        }

        return $request;
    }

    /**
     * Generates the control code is used to ensure that it is a particular
     * Merchant (and not a fraudster) that initiates the transaction.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment to generate control code
     *
     * @return      string                                          Generated control code
     */
    protected function createSignature(PaymentTransaction $paymentTransaction)
    {
        $controlCode = '';

        foreach (static::$signatureDefinition as $propertyPath)
        {
            $controlCode .= PropertyAccessor::getValue($paymentTransaction, $propertyPath);
        }

        return sha1($controlCode);
    }

    /**
     * Validates response before payment transaction updating
     * if payment transaction is processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment
     * @param       Response                $response               Response for validating
     */
    protected function validateResponseOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        if ($response->getType() !== static::$successResponseType)
        {
            throw new ValidationException("Response type '{$response->getType()}' does not match " .
                                          "success response type '" . static::$successResponseType . "'");
        }

        $missedFields   = array();

        foreach (static::$responseFieldsDefinition as $fieldName)
        {
            if (empty($response[$fieldName]))
            {
                $missedFields[] = $fieldName;
            }
        }

        if (!empty($missedFields))
        {
            throw new ValidationException("Some required fields missed or empty in Response: " .
                                          implode(', ', $missedFields) . ". \n");
        }

        $this->validateClientOrderId($paymentTransaction, $response);
    }

    /**
     * Validates response before payment transaction updating
     * if payment transaction is not processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment
     * @param       Response                $response               Response for validating
     */
    protected function validateResponseOnError(PaymentTransaction $paymentTransaction, Response $response)
    {
        $allowedTypes = array(static::$successResponseType, 'error', 'validation-error');

        if (!in_array($response->getType(), $allowedTypes))
        {
            throw new ValidationException("Unknown response type '{$response->getType()}'");
        }

        $this->validateClientOrderId($paymentTransaction, $response);
    }

    /**
     * Updates payment transaction by query response data
     * if payment transaction is processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment for updating
     * @param       Response                $response               Response for payment updating
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        if($response->isApproved())
        {
            $paymentTransaction->setProcessingStage(PaymentTransaction::STAGE_FINISHED);
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_APPROVED);
        }
        elseif($response->hasHtml() || $response->hasRedirectUrl())
        {
            $paymentTransaction->setProcessingStage(PaymentTransaction::STAGE_REDIRECTED);
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_PROCESSING);
        }
        elseif($response->isProcessing())
        {
            $paymentTransaction->setProcessingStage(PaymentTransaction::STAGE_CREATED);
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_PROCESSING);
        }

        if(strlen($response->getPaynetPaymentId()) > 0)
        {
            $paymentTransaction
                ->getPayment()
                ->setPaynetPaymentId($response->getPaynetPaymentId())
            ;
        }
    }

    /**
     * Updates payment transaction by query response data
     * if payment transaction is not processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment for updating
     * @param       Response                $response               Response for payment updating
     */
    protected function updatePaymentTransactionOnError(PaymentTransaction $paymentTransaction, Response $response)
    {
        $paymentTransaction->setProcessingStage(PaymentTransaction::STAGE_FINISHED);

        if ($response->isDeclined())
        {
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_DECLINED);
        }
        else
        {
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_ERROR);
        }
    }

    /**
     * Validates query object definition
     *
     * @throws      RuntimeException
     */
    protected function validateQueryDefinition()
    {
        if (empty(static::$requestFieldsDefinition))
        {
            throw new RuntimeException('You must configure requestFieldsDefinition property');
        }

        if (empty(static::$signatureDefinition))
        {
            throw new RuntimeException('You must configure signatureDefinition property');
        }

        if (empty(static::$responseFieldsDefinition))
        {
            throw new RuntimeException('You must configure responseFieldsDefinition property');
        }

        if (empty(static::$successResponseType))
        {
            throw new RuntimeException('You must configure allowedResponseTypes property');
        }
    }

    /**
     * Validates payment transaction query config
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     *
     * @throws      RuntimeException                                Some query config property is empty
     */
    protected function validateQueryConfig(PaymentTransaction $paymentTransaction)
    {
        if(strlen($paymentTransaction->getQueryConfig()->getSigningKey()) === 0)
        {
            throw new ValidationException("Property 'signingKey' does not defined in Payment property 'queryConfig'");
        }
    }

    /**
     * Check, is payment transaction client order id and query response client order id equal or not.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       Response                $response               Query response
     *
     * @throws ValidationException
     */
    protected function validateClientOrderId(PaymentTransaction $paymentTransaction, Response $response)
    {
        $paymentClientPaymentId = $paymentTransaction->getPayment()->getClientPaymentId();

        if (     strlen($response->getClientPaymentId()) > 0
            &&   $paymentClientPaymentId !== $response->getClientPaymentId())
        {
            throw new ValidationException("Response clientPaymentId '{$response->getClientPaymentId()}' does " .
                                          "not match Payment clientPaymentId '{$paymentClientPaymentId}'");
        }
    }
}