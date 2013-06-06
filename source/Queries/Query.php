<?PHP
namespace PaynetEasy\Paynet\Queries;

use \ArrayObject;
use \PaynetEasy\Paynet\Data\Data;
use \PaynetEasy\Paynet\Data\Customer;
use \PaynetEasy\Paynet\Data\Order;
use \PaynetEasy\Paynet\Data\Card;
use \PaynetEasy\Paynet\Responses\Response;

use \PaynetEasy\Paynet\Transport\TransportI;

use \PaynetEasy\Paynet\Exceptions\ConfigWrong;
use \PaynetEasy\Paynet\Exceptions\InvalidControlCode;

use \Exception;
use \BadMethodCallException;

/**
 * Abstract Query or Callback
 *
 */
abstract class Query
{
    const STATE_NULL        = 'null';
    const STATE_INIT        = 'init';
    const STATE_REDIRECT    = 'redirect';
    const STATE_PROCESSING  = 'processing';
    const STATE_WAIT        = 'wait';
    const STATE_END         = 'end';

    const STATUS_APPROVED   = 'approved';
    const STATUS_DECLINED   = 'declined';
    const STATUS_ERROR      = 'error';

    /**
     * Transport
     * @var \PaynetEasy\Paynet\Transport\TransportI
     */
    protected $transport;

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
     * PaynetEasy Customer info
     * @var \PaynetEasy\Paynet\Data\Customer
     */
    protected $customer;

    /**
     * PaynetEasy Order info
     * @var \PaynetEasy\Paynet\Data\Order
     */
    protected $order;

    /**
     * PaynetEasy Card info
     * @var \PaynetEasy\Paynet\Data\Card
     */
    protected $card;

    /**
     * PaynetEasy last error
     * @var \Exception
     */
    protected $error;

    /**
     * Process state
     *
     * @var string
     */
    protected $state        = self::STATE_NULL;

    /**
     * Query status
     *
     * @var string
     */
    protected $status;

    /**
     * Flag is true, if the response must be signed by the control code
     * @var boolean
     */
    protected $is_control   = false;

    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(TransportI $transport)
    {
        $this->method           = strtolower(substr(strrchr(get_class($this), '\\'), 1));

        $this->transport        = $transport;

        $this->customer         = new Data();
        $this->card             = new Data();
        $this->order            = new Data();

        $this->config           = new ArrayObject();
    }

    /**
     * Getter for Config
     * @return ArrayObject
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Method setup configuration or configuration property if
     * $config parameter is a scalar.
     *
     * @param       ArrayObject|string   $config
     * @param       mixed                $value
     * @return      Query
     */
    public function setConfig($config, $value = null)
    {
        if(is_array($config))
        {
            $config                 = new ArrayObject($config);
        }

        if($config instanceof ArrayObject)
        {
            $this->config           = $config;
        }
        else
        {
            $this->config[$config]  = $value;
        }

        return $this;
    }

    /**
     * getter for Customer
     *
     * @return \PaynetEasy\Paynet\Data\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * setter for Customer
     * @param       Customer        $customer
     *
     * @return \PaynetEasy\Paynet\Queries\Query
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer     = $customer;

        return $this;
    }

    /**
     * getter for Card
     *
     * @return \PaynetEasy\Paynet\Data\Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * setter for Card
     *
     * @param       Card        $card
     *
     * @return      \PaynetEasy\Paynet\Queries\Query
     */
    public function setCard(Card $card)
    {
        $this->card         = $card;

        return $this;
    }

    /**
     * getter for Order
     *
     * @return      Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * setter for Order
     *
     * @param       Order       $order
     *
     * @return      \PaynetEasy\Paynet\Queries\Query
     */
    public function setOrder(Order $order)
    {
        $this->order        = $order;

        return $this;
    }

    /**
     * Config validator
     *
     * @throws      ConfigWrong
     */
    public function validateConfig()
    {
        if(empty($this->config['end_point']))
        {
            throw new ConfigWrong('end_point undefined');
        }

        if(empty($this->config['control']))
        {
            throw new ConfigWrong('control undefined');
        }
    }

    /**
     * Common validator for Query
     *
     */
    public function validate()
    {
        $this->validateConfig();

        return;
    }

    /**
     * Processing Query
     *
     * @param       array       $data       Data
     *
     * @return      \PaynetEasy\Paynet\Responses\Response
     */
    public function process($data = null)
    {
        //
        // This code use as handler for callback
        //

        try
        {
            $this->validate();

            return $this->processResponse(new Response($data));
        }
        catch(Exception $e)
        {
            $this->state            = self::STATE_END;
            $this->status           = self::STATUS_ERROR;
            $this->error            = $e;

            throw $e;
        }
    }

    /**
     * Return state of query
     * @return string
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * Return status of query
     * @return string
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Method returned last error or null
     * @return Exception|null
     */
    public function getLastError()
    {
        return $this->error;
    }

    protected function createControlCode()
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
        $query                              = array
        (
            '.method'       => $this->method,
            '.end_point'    => $this->config['end_point']
        );

        $query['control']                   = $this->createControlCode();

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
     * @throws      InvalidControlCode
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
            throw new InvalidControlCode($sign, $response->control());
        }
    }

    /**
     * Handling response from the Paynet
     *
     * @param       Response        $response
     *
     * @return      \PaynetEasy\Paynet\Responses\Response
     *
     * @throws      \PaynetEasy\Paynet\Exceptions\PaynetException
     */
    protected function processResponse(Response $response)
    {
        if($this->is_control)
        {
            $this->validateControlCode($response);
        }

        if($response->isError())
        {
            throw $response->error();
        }
        elseif($response->isApproved())
        {
            $this->state        = self::STATE_END;
            $this->status       = self::STATUS_APPROVED;
        }
        // "filtered" status is interpreted as the "DECLINED"
        elseif($response->isDeclined())
        {
            $this->state        = self::STATE_END;
            $this->status       = self::STATUS_DECLINED;
        }
        // For the 3D mode is set to the state "REDIRECT"
        // or for Form API redirect_url
        elseif($response->offsetExists('html') || $response->redirectUrl())
        {
            $this->state        = self::STATE_REDIRECT;
        }
        //
        // If it does not redirect, it's processing
        elseif($response->isProcessing())
        {
            $this->state        = self::STATE_PROCESSING;
        }

        if(!is_null(($paynet_order_id = $response->paynetOrderId())))
        {
            $this->order->setPaynetOrderId($paynet_order_id);
        }

        return $response;
    }


    /**
     * Send Query
     *
     * @param       array       $query
     *
     * @return      \PaynetEasy\Paynet\Responses\Response
     *
     * @throws      \PaynetEasy\Paynet\Exceptions\PaynetException
     */
    protected function sendQuery($query)
    {
        try
        {
            return $this->processResponse($this->transport->query($query));
        }
        catch(Exception $e)
        {
            $this->state            = self::STATE_END;
            $this->status           = self::STATUS_ERROR;
            $this->error            = $e;

            throw $e;
        }
    }
}