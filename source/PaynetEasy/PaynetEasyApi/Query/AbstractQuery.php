<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\String;
use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\Utils\Validator;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentInterface;

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
     * Config for API query object
     *
     * @var array
     */
    protected $config;

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
    static protected $controlCodeDefinition = array();

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
     * @param       array       $config         API query object config
     */
    public function __construct(array $config = array())
    {
        $this->setApiMethod(get_called_class());
        $this->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    final public function createRequest(PaymentInterface $payment)
    {
        try
        {
            $this->validatePayment($payment);
        }
        catch (Exception $e)
        {
            $payment->addError($e)
                  ->setProcessingStage(PaymentInterface::STAGE_FINISHED)
                  ->setStatus(PaymentInterface::STATUS_ERROR);

            throw $e;
        }

        $request = new Request($this->paymentToRequest($payment));

        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($this->config['end_point']);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    final public function processResponse(PaymentInterface $payment, Response $response)
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
            $payment->addError($e)
                  ->setProcessingStage(PaymentInterface::STAGE_FINISHED)
                  ->setStatus(PaymentInterface::STATUS_ERROR);

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
     * @param       PaymentInterface        $payment        Payment for validation
     */
    protected function validatePayment(PaymentInterface $payment)
    {
        $errorMessage   = '';
        $missedFields   = array();
        $invalidFields  = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath, $isFieldRequired, $validationRule) = $fieldDescription;

            // generated field or field from config
            if (empty($propertyPath))
            {
                continue;
            }

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
     * @param       PaymentInterface        $payment        Payment for request
     *
     * @return      array                                   Request
     */
    protected function paymentToRequest(PaymentInterface $payment)
    {
        $request = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath, $isFieldRequired) = $fieldDescription;

            // generate control code
            if ($fieldName == 'control')
            {
                $request[$fieldName] = $this->createControlCode($payment);
            }
            // get value from config
            elseif (empty($propertyPath))
            {
                if (!empty($this->config[$fieldName]))
                {
                    $request[$fieldName] = $this->config[$fieldName];
                }
                elseif ($isFieldRequired === true)
                {
                    throw new RuntimeException("Field '{$fieldName}' missed in config");
                }
            }
            // get value from payment
            else
            {
                $fieldValue = PropertyAccessor::getValue($payment, $propertyPath);

                if (!empty($fieldValue))
                {
                    $request[$fieldName] = $fieldValue;
                }
            }
        }

        return $request;
    }

    /**
     * Generates the control code is used to ensure that it is a particular
     * Merchant (and not a fraudster) that initiates the transaction.
     *
     * @param       PaymentInterface        $payment        Payment to generate control code
     *
     * @return      string                                  Generated control code
     */
    protected function createControlCode(PaymentInterface $payment)
    {
        $controlCode = '';

        foreach (static::$controlCodeDefinition as $propertyPath)
        {
            // get value from config
            if (!empty($this->config[$propertyPath]))
            {
                $controlCode .= $this->config[$propertyPath];
            }
            // get value from payment
            else
            {
                $fieldValue = PropertyAccessor::getValue($payment, $propertyPath);

                if (!empty($fieldValue))
                {
                    $controlCode .= $fieldValue;
                }
            }
        }

        return sha1($controlCode);
    }

    /**
     * Validates response before Payment updating if Payment is processing or approved
     *
     * @param       PaymentInterface        $payment        Payment
     * @param       Response                $response       Response for validating
     */
    protected function validateResponseOnSuccess(PaymentInterface $payment, Response $response)
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
     * @param       PaymentInterface        $payment        Payment
     * @param       Response                $response       Response for validating
     */
    protected function validateResponseOnError(PaymentInterface $payment, Response $response)
    {
        $allowedTypes = array(static::$successResponseType, 'error', 'validation-error');

        if (!in_array($response->getType(), $allowedTypes))
        {
            throw new ValidationException("Unknow response type '{$response->getType()}'");
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
     * @param       PaymentInterface       $payment        Payment for updating
     * @param       Response               $response       Response for payment updating
     */
    protected function updatePaymentOnSuccess(PaymentInterface $payment, Response $response)
    {
        if($response->isApproved())
        {
            $payment->setProcessingStage(PaymentInterface::STAGE_FINISHED);
            $payment->setStatus(PaymentInterface::STATUS_APPROVED);
        }
        elseif($response->hasHtml() || $response->hasRedirectUrl())
        {
            $payment->setProcessingStage(PaymentInterface::STAGE_REDIRECTED);
            $payment->setStatus(PaymentInterface::STATUS_PROCESSING);
        }
        elseif($response->isProcessing())
        {
            $payment->setProcessingStage(PaymentInterface::STAGE_CREATED);
            $payment->setStatus(PaymentInterface::STATUS_PROCESSING);
        }

        if(strlen($response->getPaynetPaymentId()) > 0)
        {
            $payment->setPaynetPaymentId($response->getPaynetPaymentId());
        }
    }

    /**
     * Updates Payment by Response data if Payment is not processing or approved
     *
     * @param       PaymentInterface       $payment        Payment for updating
     * @param       Response               $response       Response for payment updating
     */
    protected function updatePaymentOnError(PaymentInterface $payment, Response $response)
    {
        $payment->setProcessingStage(PaymentInterface::STAGE_FINISHED);
        $payment->addError($response->getError());

        if ($response->isDeclined())
        {
            $payment->setStatus(PaymentInterface::STATUS_DECLINED);
        }
        else
        {
            $payment->setStatus(PaymentInterface::STATUS_ERROR);
        }
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
        if (empty(static::$requestFieldsDefinition))
        {
            throw new RuntimeException('You must configure requestFieldsDefinition property');
        }

        if (empty(static::$controlCodeDefinition))
        {
            throw new RuntimeException('You must configure controlCodeDefinition property');
        }

        if (empty(static::$responseFieldsDefinition))
        {
            throw new RuntimeException('You must configure responseFieldsDefinition property');
        }

        if (empty(static::$successResponseType))
        {
            throw new RuntimeException('You must configure allowedResponseTypes property');
        }

        if(empty($config['end_point']))
        {
            throw new RuntimeException('Node end_point does not defined in config');
        }

        if(empty($config['control']))
        {
            throw new RuntimeException('Node control does not defined in config');
        }

        if(empty($config['login']))
        {
            throw new RuntimeException('Node login does not defined in config');
        }

        $this->config = $config;
    }

    /**
     * Set API query method. Query class name must follow next convention:
     *
     * (API query)                  (query class name)
     * create-card-ref      =>      CreateCardRefQuery
     * return               =>      ReturnQuery
     *
     * @param       string      $class          API query object class
     */
    protected function setApiMethod($class)
    {
        if (!empty($this->apiMethod))
        {
            return;
        }

        $result = array();

        preg_match('#(?<=\\\\)\w+(?=Query$)#i', $class, $result);

        if (empty($result))
        {
            throw new RuntimeException('API method name not found in class name');
        }

        $this->apiMethod = String::uncamelize($result[0], '-');
    }
}