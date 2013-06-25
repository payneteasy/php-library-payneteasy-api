<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\String;
use PaynetEasy\Paynet\Utils\PropertyAccessor;
use PaynetEasy\Paynet\Utils\Validator;

use PaynetEasy\Paynet\OrderData\OrderInterface;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\Request;

use PaynetEasy\Paynet\Exception\ValidationException;
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
     *     [<first field name>:string,  <first order property path>:string,   <is field required>:boolean, <validation rule>:string],
     *     [<second field name>:string, <second order property path>:string,  <is field required>:boolean, <validation rule>:string],
     *     ...
     *     [<last field name>:string,   <last order property path>:string,    <is field required>:boolean, <validation rule>:string]
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
    final public function createRequest(OrderInterface $order)
    {
        try
        {
            $this->validateOrder($order);
        }
        catch (Exception $e)
        {
            $order->addError($e)
                  ->setTransportStage(OrderInterface::STAGE_ENDED)
                  ->setStatus(OrderInterface::STATUS_ERROR);

            throw $e;
        }

        $request = new Request($this->orderToRequest($order));

        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($this->config['end_point']);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    final public function processResponse(OrderInterface $order, Response $response)
    {
        if(   !$response->isProcessing()
           && !$response->isApproved())
        {
            $validate = array($this, 'validateResponseOnError');
            $update   = array($this, 'updateOrderOnError');
        }
        else
        {
            $validate = array($this, 'validateResponseOnSuccess');
            $update   = array($this, 'updateOrderOnSuccess');
        }

        try
        {
            call_user_func($validate, $order, $response);
        }
        catch (Exception $e)
        {
            $order->addError($e)
                  ->setTransportStage(OrderInterface::STAGE_ENDED)
                  ->setStatus(OrderInterface::STATUS_ERROR);

            throw $e;
        }

        call_user_func($update, $order, $response);

        return $response;
    }

    /**
     * Validates order before query constructing
     *
     * @param       OrderInterface          $order          Order for validation
     */
    protected function validateOrder(OrderInterface $order)
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

            $fieldValue = PropertyAccessor::getValue($order, $propertyPath, false);

            if (!empty($fieldValue))
            {
                try
                {
                    Validator::validateByRule($fieldValue, $validationRule);
                }
                catch (ValidationException $e)
                {
                    $invalidFields[] = $e->getMessage();
                }
            }
            elseif ($isFieldRequired)
            {
                $missedFields[] = $fieldName;
            }
        }

        if (!empty($missedFields))
        {
            $errorMessage .= "Some required fields missed or empty in Order: " .
                             implode(', ', $missedFields) . ". \n";
        }

        if (!empty($invalidFields))
        {
            $errorMessage .= "Some fields invalid in Order: " .
                             implode(', ', $invalidFields) . ". \n";
        }

        if (!empty($errorMessage))
        {
            throw new ValidationException($errorMessage);
        }
    }

    /**
     * Creates request from Order
     *
     * @param       OrderInterface          $order          Order for request
     *
     * @return      array                                   Request
     */
    protected function orderToRequest(OrderInterface $order)
    {
        $request = array();

        foreach (static::$requestFieldsDefinition as $fieldDescription)
        {
            list($fieldName, $propertyPath, $isFieldRequired) = $fieldDescription;

            // generate control code
            if ($fieldName == 'control')
            {
                $request[$fieldName] = $this->createControlCode($order);
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
            // get value from order
            else
            {
                $fieldValue = PropertyAccessor::getValue($order, $propertyPath);

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
     * @param       OrderInterface          $order          Order to generate control code
     *
     * @return      string                                  Generated control code
     */
    protected function createControlCode(OrderInterface $order)
    {
        $controlCode = '';

        foreach (static::$controlCodeDefinition as $propertyPath)
        {
            // get value from config
            if (!empty($this->config[$propertyPath]))
            {
                $controlCode .= $this->config[$propertyPath];
            }
            // get value from order
            else
            {
                $fieldValue = PropertyAccessor::getValue($order, $propertyPath);

                if (!empty($fieldValue))
                {
                    $controlCode .= $fieldValue;
                }
            }
        }

        return sha1($controlCode);
    }

    /**
     * Validates response before Order updating if Order is processing or approved
     *
     * @param       OrderInterface          $order          Order
     * @param       Response                $response       Response for validating
     */
    protected function validateResponseOnSuccess(OrderInterface $order, Response $response)
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

        if (     strlen($response->getClientOrderId()) > 0
            &&   $order->getClientOrderId() !== $response->getClientOrderId())
        {
            throw new ValidationException("Response client_orderid '{$response->getClientOrderId()}' does " .
                                          "not match Order client_orderid '{$order->getClientOrderId()}'");
        }
    }

    /**
     * Validates response before Order updating if Order is not processing or approved
     *
     * @param       OrderInterface          $order          Order
     * @param       Response                $response       Response for validating
     */
    protected function validateResponseOnError(OrderInterface $order, Response $response)
    {
        $allowedTypes = array(static::$successResponseType, 'error', 'validation-error');

        if (!in_array($response->getType(), $allowedTypes))
        {
            throw new ValidationException("Unknow response type '{$response->getType()}'");
        }

        if (     strlen($response->getClientOrderId()) > 0
            &&   $order->getClientOrderId() !== $response->getClientOrderId())
        {
            throw new ValidationException("Response client_orderid '{$response->getClientOrderId()}' does " .
                                          "not match Order client_orderid '{$order->getClientOrderId()}'");
        }
    }

    /**
     * Updates Order by Response data if Order is processing or approved
     *
     * @param       OrderInterface         $order          Order for updating
     * @param       Response               $response       Response for order updating
     */
    protected function updateOrderOnSuccess(OrderInterface $order, Response $response)
    {
        if($response->isApproved())
        {
            $order->setTransportStage(OrderInterface::STAGE_ENDED);
            $order->setStatus(OrderInterface::STATUS_APPROVED);
        }
        elseif($response->hasHtml() || $response->hasRedirectUrl())
        {
            $order->setTransportStage(OrderInterface::STAGE_REDIRECTED);
            $order->setStatus(OrderInterface::STATUS_PROCESSING);
        }
        elseif($response->isProcessing())
        {
            $order->setTransportStage(OrderInterface::STAGE_CREATED);
            $order->setStatus(OrderInterface::STATUS_PROCESSING);
        }

        if(strlen($response->getPaynetOrderId()) > 0)
        {
            $order->setPaynetOrderId($response->getPaynetOrderId());
        }
    }

    /**
     * Updates Order by Response data if Order is not processing or approved
     *
     * @param       OrderInterface         $order          Order for updating
     * @param       Response               $response       Response for order updating
     */
    protected function updateOrderOnError(OrderInterface $order, Response $response)
    {
        $order->setTransportStage(OrderInterface::STAGE_ENDED);
        $order->addError($response->getError());

        if ($response->isDeclined())
        {
            $order->setStatus(OrderInterface::STATUS_DECLINED);
        }
        else
        {
            $order->setStatus(OrderInterface::STATUS_ERROR);
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