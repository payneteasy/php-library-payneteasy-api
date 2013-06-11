<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Exceptions\ConfigException;
use PaynetEasy\Paynet\Data\OrderInterface;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Order_status
 */
class StatusQuery extends AbstractQuery
{
    public function validateOrder(OrderInterface $order)
    {
        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        $order->validateShort();
    }

    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query              = array_merge
        (
            $order->getContextData(),
            // Выделить этот код в отдельный класс
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode($order)
            )
        );

        return $this->wrapToRequest($query);
    }

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