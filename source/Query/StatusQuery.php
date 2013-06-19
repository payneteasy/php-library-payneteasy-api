<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Order_status
 */
class StatusQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getContextData(),
            $this->commonQueryOptions($order)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        $order->validateShort();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // This is SHA-1 checksum of the concatenation
        // login + client-order-id + paynet-order-id + merchant-control.
        return sha1
        (
            $this->config['login'].
            $order->getOrderCode().
            $order->getPaynetOrderId().
            $this->config['control']
        );
    }
}