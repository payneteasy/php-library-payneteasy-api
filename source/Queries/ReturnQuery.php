<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    protected $comment;

    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Defines comment for Transaction
     *
     * @param string        $comment
     *
     * @return \PaynetEasy\Paynet\Queries\ReturnQuery
     */
    public function setComment($comment)
    {
        $this->comment          = $comment;

        return $this;
    }

    public function validateOrder(OrderInterface $order)
    {
        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(strlen($this->comment) > 50)
        {
            throw new ConfigException('comment is very big (over 50 chars)');
        }

        $order->validateShort();
    }

    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query              = array_merge
        (
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode($order)
            ),
            $order->getContextData()
        );

        /**
         * @todo Amount MUST be setted in the order
         */
        if($order->getAmount())
        {
            $query['amount']    = $order->getAmount();
            $query['currency']  = $order->getCurrency();
        }

        if(!empty($this->comment))
        {
            $query['comment']       = $this->comment;
        }

        return $this->wrapToRequest($query);
    }

    protected function createControlCode(OrderInterface $order)
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
            $order->getOrderCode(),
            $order->getPaynetOrderId()
        );

        /**
         * @todo Amount MUST be setted in the order
         */
        if($order->getAmount())
        {
            $sign[]             = $order->getAmountInCents();
            $sign[]             = $order->getCurrency();
        }

        $sign[]                 = $this->config['control'];

        return sha1(implode('', $sign));
    }
}