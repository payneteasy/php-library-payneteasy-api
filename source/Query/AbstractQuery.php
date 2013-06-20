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
     * [<field name>:string, <order property path>:string,  <is field required>:boolean, <validation rule>:string]
     *
     * @var array
     */
    static protected $requestFieldsDefinition = array();

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
                  ->setState(OrderInterface::STATE_END)
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
        try
        {
            $this->validateResponse($order, $response);
        }
        catch (Exception $e)
        {
            $order->addError($e)
                  ->setState(OrderInterface::STATE_END)
                  ->setStatus(OrderInterface::STATUS_ERROR);

            throw $e;
        }

        $this->updateOrder($order, $response);

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
    abstract protected function createControlCode(OrderInterface $order);

    /**
     * Validates response before Order updating
     *
     * @param       OrderInterface          $order          Order
     * @param       Response                $response       Response for validating
     */
    protected function validateResponse(OrderInterface $order, Response $response)
    {
        if (    !$response->isError()
            &&   $order->getClientOrderId() !== $response->orderId())
        {
            throw new ValidationException("Response client_orderid '{$response->orderId()}' does " .
                                          "not match Order client_orderid '{$order->getClientOrderId()}'");
        }
    }

    /**
     * Updates Order by Response data
     *
     * @param       OrderInterface         $order          Order for updating
     * @param       Response               $response       Response for order updating
     */
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        if($response->isError())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_ERROR);
            $order->addError($response->error());
        }
        elseif($response->isApproved())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_APPROVED);
        }
        // "filtered" status is interpreted as the "DECLINED"
        elseif($response->isDeclined())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_DECLINED);
        }
        // For the 3D mode is set to the state "REDIRECT"
        // or for Form API redirect_url
        elseif($response->hasHtml() || $response->hasRedirectUrl())
        {
            $order->setState(OrderInterface::STATE_REDIRECT);
        }
        //
        // If it does not redirect, it's processing
        elseif($response->isProcessing())
        {
            $order->setState(OrderInterface::STATE_PROCESSING);
            $order->setStatus(OrderInterface::STATUS_PROCESSING);
        }

        if(strlen($response->paynetOrderId()) > 0)
        {
            $order->setPaynetOrderId($response->paynetOrderId());
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

    /**
     * Method forms the common parameters for the query
     *
     * @return      array
     */
    protected function commonQueryOptions()
    {
        $commonOptions = array
        (
            'login'     => $this->config['login'],
            'end_point' => $this->config['end_point']
        );

        if(isset($this->config['redirect_url']))
        {
            $commonOptions['redirect_url']          = $this->config['redirect_url'];
        }

        if(isset($this->config['server_callback_url']))
        {
            $commonOptions['server_callback_url']   = $this->config['server_callback_url'];
        }

        return $commonOptions;
    }

    /**
     * Wrap query data by Request object
     *
     * @param       array       $query                          Query data
     *
     * @return      \PaynetEasy\Paynet\Transport\Request        Request object
     */
    protected function wrapToRequest(array $query)
    {
        $request = new Request($query);
        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($this->config['end_point']);

        return $request;
    }
}