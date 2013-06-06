<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\Order;

use \PaynetEasy\Paynet\Transport\TransportI;

use \PaynetEasy\Paynet\Exceptions\ResponseException;
use \PaynetEasy\Paynet\Exceptions\ConfigWrong;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class   CreateCardRef           extends Query
{
    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(TransportI $transport)
    {
        parent::__construct($transport);

        $this->method       = 'create-card-ref';
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

        $this->order->validateShort();
    }

    public function process($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->order->getContextData(),
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode(),
                '.method'       => $this->method,
                '.end_point'    => $this->config['end_point']
            )
        );

        $response           = $this->sendQuery($query);

        if(!isset($response['card-ref-id']))
        {
            $e              = new ResponseException('card-ref-id undefined');
            $this->error    = $e;
            $this->state    = self::STATE_END;
            throw $e;
        }

        $response['cardrefid'] = $response['card-ref-id'];
        unset($response['card-ref-id']);

        return $response;
    }

    protected function createControlCode()
    {
        // This is SHA-1 checksum of the concatenation
        // login + client-order-id + paynet-order-id + merchant-control.
        return sha1
        (
            $this->config['login'].
            $this->order->getOrderCode().
            $this->order->getPaynetOrderId().
            $this->config['control']
        );
    }
}