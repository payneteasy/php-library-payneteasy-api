<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\Utils\Validator;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

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
    final public function createRequest(Payment $payment)
    {
        try
        {
            $this->validatePayment($payment);
        }
        catch (Exception $e)
        {
            $payment
                  ->setProcessingStage(Payment::STAGE_FINISHED)
                  ->setStatus(Payment::STATUS_ERROR);

            throw $e;
        }

        $request = new Request($this->paymentToRequest($payment));

        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($payment->getQueryConfig()->getEndPoint())
                ->setGatewayUrl($payment->getQueryConfig()->getGatewayUrl())
                ->setSignature($this->createSignature($payment));

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    final public function processResponse(Payment $payment, Response $response)
    {
        if(   !$response->isProcessing()
           && !$response->isApproved())
        {
            $validate = array($this, 'validateResponseOnError');
            $update   = array($this, 'updatePaymentOnError');
        }
        else
        {
            $validate = array($this, 'validateResponseOnSuccess');
            $update   = array($this, 'updatePaymentOnSuccess');
        }

        try
        {
            call_user_func($validate, $payment, $response);
        }
        catch (Exception $e)
        {
            $payment
                  ->setProcessingStage(Payment::STAGE_FINISHED)
                  ->setStatus(Payment::STATUS_ERROR);

            throw $e;
        }

        call_user_func($update, $payment, $response);

        if ($response->isError())
        {
            throw $response->getError();
        }

        return $response;
    }

    /**
     * Validates payment before query constructing
     *
     * @param       Payment        $payment        Payment for validation
     */
    protected function validatePayment(Payment $payment)
    {
        $this->validateQueryConfig($payment);

        $errorMessage   = '';
        $missedFields   = array();
        $invalidFields  = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath, $isFieldRequired, $validationRule) = $fieldDescription;

            $fieldValue = PropertyAccessor::getValue($payment, $propertyPath, false);

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
     * Creates request from Payment
     *
     * @param       Payment        $payment        Payment for request
     *
     * @return      array                          Request
     */
    protected function paymentToRequest(Payment $payment)
    {
        $request = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath) = $fieldDescription;

            $fieldValue = PropertyAccessor::getValue($payment, $propertyPath);

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
     * @param       Payment        $payment        Payment to generate control code
     *
     * @return      string                         Generated control code
     */
    protected function createSignature(Payment $payment)
    {
        $controlCode = '';

        foreach (static::$signatureDefinition as $propertyPath)
        {
            $controlCode .= PropertyAccessor::getValue($payment, $propertyPath);
        }

        return sha1($controlCode);
    }

    /**
     * Validates response before Payment updating if Payment is processing or approved
     *
     * @param       Payment        $payment        Payment
     * @param       Response       $response       Response for validating
     */
    protected function validateResponseOnSuccess(Payment $payment, Response $response)
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

        if (     strlen($response->getClientPaymentId()) > 0
            &&   $payment->getClientPaymentId() != $response->getClientPaymentId())
        {
            throw new ValidationException("Response clientPaymentId '{$response->getClientPaymentId()}' does " .
                                          "not match Payment clientPaymentId '{$payment->getClientPaymentId()}'");
        }
    }

    /**
     * Validates response before Payment updating if Payment is not processing or approved
     *
     * @param       Payment        $payment        Payment
     * @param       Response       $response       Response for validating
     */
    protected function validateResponseOnError(Payment $payment, Response $response)
    {
        $allowedTypes = array(static::$successResponseType, 'error', 'validation-error');

        if (!in_array($response->getType(), $allowedTypes))
        {
            throw new ValidationException("Unknown response type '{$response->getType()}'");
        }

        if (     strlen($response->getClientPaymentId()) > 0
            &&   $payment->getClientPaymentId() !== $response->getClientPaymentId())
        {
            throw new ValidationException("Response clientPaymentId '{$response->getClientPaymentId()}' does " .
                                          "not match Payment clientPaymentId '{$payment->getClientPaymentId()}'");
        }
    }

    /**
     * Updates Payment by Response data if Payment is processing or approved
     *
     * @param       Payment       $payment        Payment for updating
     * @param       Response      $response       Response for payment updating
     */
    protected function updatePaymentOnSuccess(Payment $payment, Response $response)
    {
        if($response->isApproved())
        {
            $payment->setProcessingStage(Payment::STAGE_FINISHED);
            $payment->setStatus(Payment::STATUS_APPROVED);
        }
        elseif($response->hasHtml() || $response->hasRedirectUrl())
        {
            $payment->setProcessingStage(Payment::STAGE_REDIRECTED);
            $payment->setStatus(Payment::STATUS_PROCESSING);
        }
        elseif($response->isProcessing())
        {
            $payment->setProcessingStage(Payment::STAGE_CREATED);
            $payment->setStatus(Payment::STATUS_PROCESSING);
        }

        if(strlen($response->getPaynetPaymentId()) > 0)
        {
            $payment->setPaynetPaymentId($response->getPaynetPaymentId());
        }
    }

    /**
     * Updates Payment by Response data if Payment is not processing or approved
     *
     * @param       Payment       $payment        Payment for updating
     * @param       Response      $response       Response for payment updating
     */
    protected function updatePaymentOnError(Payment $payment, Response $response)
    {
        $payment->setProcessingStage(Payment::STAGE_FINISHED);

        if ($response->isDeclined())
        {
            $payment->setStatus(Payment::STATUS_DECLINED);
        }
        else
        {
            $payment->setStatus(Payment::STATUS_ERROR);
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
     * Validates payment query config
     *
     * @param       Payment         $payment        Payment
     *
     * @throws      RuntimeException                Some query config property is empty
     */
    public function validateQueryConfig(Payment $payment)
    {
        if(strlen($payment->getQueryConfig()->getSigningKey()) === 0)
        {
            throw new ValidationException("Property 'signingKey' does not defined in Payment property 'queryConfig'");
        }
    }
}