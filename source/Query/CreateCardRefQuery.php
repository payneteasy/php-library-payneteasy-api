<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use RuntimeException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class CreateCardRefQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getContextData(),
            $this->commonQueryOptions()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        $order->validateShort();
        $this->checkOrderState($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponse(OrderInterface $order, Response $response)
    {
        if(!isset($response['card-ref-id']))
        {
            throw new RuntimeException('card-ref-id undefined');
        }

        $this->checkOrderState($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        if($response->isApproved())
        {
            $order->createRecurrentCardFrom($response['card-ref-id']);
        }

        parent::updateOrder($order, $response);
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

    /**
     * Check Order state and status.
     * State must be STATE_END and status must be STATUS_APPROVED.
     *
     * @param       OrderInterface      $order      Order for checking
     */
    protected function checkOrderState(OrderInterface $order)
    {
        if (    $order->getState()  !== OrderInterface::STATE_END
            ||  $order->getStatus() !== OrderInterface::STATUS_APPROVED)
        {
            throw new RuntimeException('Only approved Order can be used for create-card-ref-id');
        }
    }
}