<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use RuntimeException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfoQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getRecurrentCardFrom()->getData(),
            $this->commonQueryOptions($order)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        parent::updateOrder($order, $response);

        /**
         * @todo Remove CardInfo, use RecurrentCard instead
         */
//        return new CardInfo($response->getArrayCopy());
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        if(!$order->hasRecurrentCardFrom())
        {
            throw new RuntimeException('Order is not instance of Order');
        }

        $order->getRecurrentCardFrom()->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // This is SHA-1 checksum of the concatenation
        // login + cardrefid + merchant-control.
        return sha1
        (
            $this->config['login'].
            $order->getRecurrentCardFrom()->getCardRefId().
            $this->config['control']
        );
    }
}