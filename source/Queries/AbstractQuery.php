<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\Request;

use PaynetEasy\Paynet\Exceptions\ConfigException;
use PaynetEasy\Paynet\Exceptions\InvalidControlCodeException;

use BadMethodCallException;

/**
 * Abstract Query
 */
abstract class  AbstractQuery
implements      QueryInterface
{
    /**
     * Config for PaynetEasy
     * @var \ArrayObject
     */
    protected $config;

    /**
     * Method API
     * @var string
     */
    protected $method;

    /**
     * Flag is true, if the response must be signed by the control code
     * @var boolean
     */
    protected $is_control   = false;

    /**
     * Constructor
     *
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
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        return $this->wrapToRequest($order->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(OrderInterface $order, Response $response)
    {
        if($this->is_control)
        {
            $this->validateControlCode($response);
        }

        if($response->isError())
        {
            $order->setState(OrderInterface::STATE_END);
            $order->setStatus(OrderInterface::STATUS_ERROR);
            $order->addError($response->error());

            throw $response->error();
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
        elseif($response->offsetExists('html') || $response->redirectUrl())
        {
            $order->setState(OrderInterface::STATE_REDIRECT);
        }
        //
        // If it does not redirect, it's processing
        elseif($response->isProcessing())
        {
            $order->setState(OrderInterface::STATE_PROCESSING);
        }

        if(!is_null(($paynet_order_id = $response->paynetOrderId())))
        {
            $order->setPaynetOrderId($paynet_order_id);
        }

        return $response;
    }

    /**
     * Set query object config
     *
     * @param       array       $config         API query object config
     *
     * @throws      ConfigException
     */
    protected function setConfig(array $config)
    {
        if(empty($config['end_point']))
        {
            throw new ConfigException('end_point undefined');
        }

        if(empty($config['control']))
        {
            throw new ConfigException('control undefined');
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
        $name_chunks    = preg_split('/(?=[A-Z])/', $class, null, PREG_SPLIT_NO_EMPTY);
        array_pop($name_chunks); // delete node "Query" from end of method name array
        $this->method   = strtolower(implode('-', $name_chunks));
    }

    /**
     * Common validator for Query
     *
     */
    /**
     * @todo Get common code from other classes
     */
    protected function validateOrder(OrderInterface $order)
    {

    }

    /**
     * @todo Delete this
     */
    protected function createControlCode(OrderInterface $order)
    {
        throw new BadMethodCallException('method must be overloaded');
    }

    /**
     * Method forms the common parameters for the query
     *
     * @return      array
     */
    protected function commonQueryOptions()
    {
        $query = array
        (
            'control'       => $this->createControlCode()
        );

        if(isset($this->config['redirect_url']))
        {
            $query['redirect_url']          = $this->config['redirect_url'];
        }

        if(isset($this->config['server_callback_url']))
        {
            $query['server_callback_url']   = $this->config['server_callback_url'];
        }

        return $query;
    }

    /**
     * Validate control code
     *
     * @param       Response      $response
     *
     * @throws      InvalidControlCodeException
     */
    protected function validateControlCode(Response $response)
    {
        // This is SHA-1 checksum of the concatenation
        // status + orderid + client_orderid + merchant-control.
        $sign   = sha1
        (
            $response->status().
            $response->paynetOrderId().
            $response->orderId().
            $this->config['control']
        );

        if($sign !== $response->control())
        {
            throw new InvalidControlCodeException($sign, $response->control());
        }
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
        $request->setApiMethod($this->method)
                ->setEndPoint($this->config['end_point']);

        return $request;
    }
}