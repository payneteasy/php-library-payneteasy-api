<?php

namespace PaynetEasy\PaynetEasyApi\Query\Prototype;

use PaynetEasy\PaynetEasyApi\Query\QueryInterface;

use PaynetEasy\PaynetEasyApi\Util\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\Util\Validator;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;

use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Transport\Request;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use RuntimeException;
use Exception;

/**
 * Abstract Query
 */
abstract class Query implements QueryInterface
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
     *     [<first field name>:string,  <first property path>:string,   <is field required>:boolean, <validation rule>:string],
     *     [<second field name>:string, <second property path>:string,  <is field required>:boolean, <validation rule>:string],
     *     ...
     *     [<last field name>:string,   <last property path>:string,    <is field required>:boolean, <validation rule>:string]
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
    public function createRequest(PaymentTransaction $paymentTransaction)
    {
        try
        {
            $this->validatePaymentTransaction($paymentTransaction);
        }
        catch (Exception $e)
        {
            $paymentTransaction
                ->addError($e)
                ->setStatus(PaymentTransaction::STATUS_ERROR)
            ;

            throw $e;
        }

        $request = $this->paymentTransactionToRequest($paymentTransaction);

        $request
            ->setApiMethod($this->apiMethod)
            ->setEndPoint($paymentTransaction->getQueryConfig()->getEndPoint())
            ->setEndPointGroup($paymentTransaction->getQueryConfig()->getEndPointGroup())
            ->setGatewayUrl($paymentTransaction->getQueryConfig()->getGatewayUrl())
            ->setSignature($this->createSignature($paymentTransaction))
        ;

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(PaymentTransaction $paymentTransaction, Response $response)
    {
        if(   $response->isProcessing()
           || $response->isApproved())
        {
            $validate = array($this, 'validateResponseOnSuccess');
            $update   = array($this, 'updatePaymentTransactionOnSuccess');
        }
        else
        {
            $validate = array($this, 'validateResponseOnError');
            $update   = array($this, 'updatePaymentTransactionOnError');
        }

        try
        {
            call_user_func($validate, $paymentTransaction, $response);
        }
        catch (Exception $e)
        {
            $paymentTransaction
                ->addError($e)
                ->setStatus(PaymentTransaction::STATUS_ERROR)
            ;

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
     * Validates payment transaction before request constructing
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
                    $invalidFields[] = "Field '{$fieldName}' from property path '{$propertyPath}', {$e->getMessage()}.";
                }
            }
            elseif ($isFieldRequired)
            {
                $missedFields[] = "Field '{$fieldName}' from property path '{$propertyPath}' missed or empty.";
            }
        }

        if (!empty($missedFields))
        {
            $errorMessage .= "Some required fields missed or empty in PaymentTransaction: \n" .
                             implode("\n", $missedFields) . "\n";
        }

        if (!empty($invalidFields))
        {
            $errorMessage .= "Some fields invalid in PaymentTransaction: \n" .
                             implode("\n", $invalidFields) . "\n";
        }

        if (!empty($errorMessage))
        {
            throw new ValidationException($errorMessage);
        }
    }

    /**
     * Creates request from payment transaction
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for request constructing
     *
     * @return      Request                                         Request object
     */
    protected function paymentTransactionToRequest(PaymentTransaction $paymentTransaction)
    {
        $requestFields = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath) = $fieldDescription;

            $fieldValue = PropertyAccessor::getValue($paymentTransaction, $propertyPath);

            if (!empty($fieldValue))
            {
                $requestFields[$fieldName] = $fieldValue;
            }
        }

        return new Request($requestFields);
    }

    /**
     * Generates the control code is used to ensure that it is a particular
     * Merchant (and not a fraudster) that initiates the transaction.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction to generate control code
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
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
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

        $this->validateClientId($paymentTransaction, $response);
    }

    /**
     * Validates response before payment transaction updating
     * if payment transaction is not processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       Response                $response               Response for validating
     */
    protected function validateResponseOnError(PaymentTransaction $paymentTransaction, Response $response)
    {
        $allowedTypes = array(static::$successResponseType, 'error', 'validation-error');

        if (!in_array($response->getType(), $allowedTypes))
        {
            throw new ValidationException("Unknown response type '{$response->getType()}'");
        }

        $this->validateClientId($paymentTransaction, $response);
    }

    /**
     * Updates payment transaction by query response data
     * if payment transaction is processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for updating
     * @param       Response                $response               Response for payment transaction updating
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        $paymentTransaction->setStatus($response->getStatus());
        $this->setPaynetId($paymentTransaction, $response);
    }

    /**
     * Updates payment transaction by query response data
     * if payment transaction is not processing or approved
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for updating
     * @param       Response                $response               Response for payment transaction updating
     */
    protected function updatePaymentTransactionOnError(PaymentTransaction $paymentTransaction, Response $response)
    {
        if ($response->isDeclined())
        {
            $paymentTransaction->setStatus($response->getStatus());
        }
        else
        {
            $paymentTransaction->setStatus(PaymentTransaction::STATUS_ERROR);
        }

        $paymentTransaction->addError($response->getError());

        $this->setPaynetId($paymentTransaction, $response);
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
        $queryConfig = $paymentTransaction->getQueryConfig();

        if(strlen($queryConfig->getSigningKey()) === 0)
        {
            throw new ValidationException("Property 'signingKey' does not defined in PaymentTransaction property 'queryConfig'");
        }

        if (strlen($queryConfig->getEndPoint()) == 0 && strlen($queryConfig->getEndPointGroup()) === 0)
        {
            throw new ValidationException(
                "Properties 'endPont' and 'endPointGroup' do not defined in " .
                "PaymentTransaction property 'queryConfig'. Set one of them."
            );
        }

        if (strlen($queryConfig->getEndPoint()) > 0 && strlen($queryConfig->getEndPointGroup()) > 0)
        {
            throw new ValidationException(
                "Property 'endPont' was set and property 'endPointGroup' was set in " .
                "PaymentTransaction property 'queryConfig'. Set only one of them."
            );
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
    protected function validateClientId(PaymentTransaction $paymentTransaction, Response $response)
    {
        $paymentClientId  = $paymentTransaction->getPayment()->getClientId();
        $responseClientId = $response->getPaymentClientId();

        if (     strlen($responseClientId) > 0
            &&   $paymentClientId != $responseClientId)
        {
            throw new ValidationException("Response clientId '{$responseClientId}' does " .
                                          "not match Payment clientId '{$paymentClientId}'");
        }
    }

    /**
     * Set PaynetEasy payment id to payment transaction Payment
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction
     * @param       Response                $response               Query response
     */
    protected function setPaynetId(PaymentTransaction $paymentTransaction, Response $response)
    {
        $responsePaynetId = $response->getPaymentPaynetId();

        if(strlen($responsePaynetId) > 0)
        {
            $paymentTransaction
                ->getPayment()
                ->setPaynetId($responsePaynetId)
            ;
        }
    }
}