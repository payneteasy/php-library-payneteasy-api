<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Response\CardInfo;
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
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query = array_merge
        (
            $order->getRecurrentCard()->getData(),
            $this->commonQueryOptions(),
            $this->createControlCode($order)
        );

        return $this->wrapToRequest($query);
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(OrderInterface $order, Response $response)
    {
        parent::processResponse($order, $response);

        return new CardInfo($response->getArrayCopy());
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        if(!$order->hasRecurrentCard())
        {
            throw new RuntimeException('Order is not instance of Order');
        }

        $order->getRecurrentCard()->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // This is SHA-1 checksum of the concatenation
        // login + cardrefid + merchant-control.
        return array('control' => sha1
        (
            $this->config['login'].
            $order->getRecurrentCard()->cardRefId().
            $this->config['control']
        ));
    }
}