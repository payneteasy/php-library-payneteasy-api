<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Data\OrderInterface;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\Request;

use RuntimeException;
use PaynetEasy\Paynet\Exception\InvalidControlCodeException;

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
    protected $apiMethod;

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
        if($response->control())
        {
            $this->validateControlCode($response);
        }

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
        }

        if(strlen($response->paynetOrderId()) > 0)
        {
            $order->setPaynetOrderId($response->paynetOrderId());
        }

        return $response;
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
        if(empty($config['end_point']))
        {
            throw new RuntimeException('end_point undefined');
        }

        if(empty($config['control']))
        {
            throw new RuntimeException('control undefined');
        }

        if(empty($config['login']))
        {
            throw new RuntimeException('login undefined');
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

        $name_chunks     = preg_split('/(?=[A-Z])/', $result[0], null, PREG_SPLIT_NO_EMPTY);
        $this->apiMethod = strtolower(implode('-', $name_chunks));
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
        $order->validateShort();
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
        $request->setApiMethod($this->apiMethod)
                ->setEndPoint($this->config['end_point']);

        return $request;
    }
}