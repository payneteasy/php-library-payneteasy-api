<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * The implementation of the query Capture
 * http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions#Process_Capture_Transaction
 */
class CaptureQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getContextData(),
            $this->commonQueryOptions(),
            array
            (
                'amount'    => $order->getAmount(),
                'currency'  => $order->getCurrency()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        $order->validateShort();

        if (strlen($order->getAmount()) == 0)
        {
            throw new ValidationException('Amount must be defined');
        }

        if (strlen($order->getCurrency()) == 0)
        {
            throw new ValidationException('Currency must be defined');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // Checksum used to ensure that it is Merchant (and not a fraudster)
        // that initiates the return request.
        // This is SHA-1 checksum of the concatenation login + client_orderid + orderid + merchant-control
        // if amount is not specified,
        // and login + client_orderid + orderid + amount_in_cents +
        // currency + merchant-control if amount is specified
        return sha1
        (
            $this->config['login'] .
            $order->getOrderCode() .
            $order->getPaynetOrderId() .
            $order->getAmountInCents() .
            $order->getCurrency() .
            $this->config['control']
        );
    }
}