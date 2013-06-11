<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Responses\CardInfo;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfoQuery extends AbstractQuery
{
    public function validateOrder(OrderInterface $order)
    {
        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(!$order->hasRecurrentCard())
        {
            throw new ConfigException('Order is not instance of Order');
        }

        $order->getRecurrentCard()->validate();
    }

    /**
     * Return CardInfo
     *
     * @return \PaynetEasy\Paynet\Responses\CardInfo
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query              = array_merge
        (
            $order->getRecurrentCard()->getData(),
            // Выделить этот код в отдельный класс
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode($order)
            )
        );

        return $this->wrapToRequest($query);
    }

    public function processResponse(OrderInterface $order, Response $response)
    {
        parent::processResponse($order, $response);

        return new CardInfo($response->getArrayCopy());
    }

    protected function createControlCode(OrderInterface $order)
    {
        // This is SHA-1 checksum of the concatenation
        // login + cardrefid + merchant-control.
        return sha1
        (
            $this->config['login'].
            $order->getRecurrentCard()->cardRefId().
            $this->config['control']
        );
    }
}