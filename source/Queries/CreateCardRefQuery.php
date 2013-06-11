<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exceptions\ResponseException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class CreateCardRefQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query              = array_merge
        (
            $order->getContextData(),
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode($order)
            )
        );

        return $this->wrapToRequest($query);
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(OrderInterface $order, Response $response)
    {
        if(!isset($response['card-ref-id']))
        {
            $e              = new ResponseException('card-ref-id undefined');
            $order->addError($e);
            $order->setState(OrderInterface::STATE_END);
            throw $e;
        }

        $response['cardrefid'] = $response['card-ref-id'];
        unset($response['card-ref-id']);

        parent::processResponse($order, $response);
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