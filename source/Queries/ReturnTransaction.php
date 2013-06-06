<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\Order;

use \PaynetEasy\Paynet\Exceptions\ConfigWrong;

use \PaynetEasy\Paynet\Transport\TransportI;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnTransaction extends Query
{
    protected $comment;

    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(TransportI $transport)
    {
        parent::__construct($transport);

        $this->method           = 'return';
    }

    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Defines comment for Transaction
     *
     * @param string        $comment
     *
     * @return \PaynetEasy\Paynet\Queries\ReturnTransaction
     */
    public function setComment($comment)
    {
        $this->comment          = $comment;

        return $this;
    }

    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigWrong('login undefined');
        }

        if(($this->order instanceof Order) === false)
        {
            throw new ConfigWrong('Order is not instance of Order');
        }

        if(strlen($this->comment) > 50)
        {
            throw new ConfigWrong('comment is very big (over 50 chars)');
        }

        $this->order->validateShort();
    }

    public function process($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode(),
                '.method'       => $this->method,
                '.end_point'    => $this->config['end_point']
            ),
            $this->order->getContextData()
        );

        if($this->order->getAmount())
        {
            $query          = array_merge
            (
                $query,
                array
                (
                    'amount'    => $this->order->getAmount(),
                    'currency'  => $this->order->getCurrency(),
                )
            );
        }

        if(!empty($this->comment))
        {
            $query['comment']       = $this->comment;
        }

        return $this->sendQuery($query);
    }

    protected function createControlCode()
    {
        // Checksum used to ensure that it is Merchant (and not a fraudster)
        // that initiates the return request.
        // This is SHA-1 checksum of the concatenation login + client_orderid + orderid + merchant-control
        // if amount is not specified,
        // and login + client_orderid + orderid + amount_in_cents +
        // currency + merchant-control if amount is specified
        $sign                   = array
        (
            $this->config['login'],
            $this->order->getOrderCode(),
            $this->order->getPaynetOrderId()
        );

        if($this->order->getAmount())
        {
            $sign[]             = $this->order->getAmountInCents();
            $sign[]             = $this->order->getCurrency();
        }

        $sign[]                 = $this->config['control'];

        return sha1(implode('', $sign));
    }
}