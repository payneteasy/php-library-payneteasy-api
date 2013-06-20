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
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                    true,   '#^[\S\s]{1,128}$#i'),
        array('orderid',            'paynetOrderId',                    true,   '#^[\S\s]{1,20}$#i'),
        // generated
        array('control',             null,                              true,    null),
        // from config
        array('login',               null,                              true,    null)
    );

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
            $order->getClientOrderId().
            $order->getPaynetOrderId().
            $this->config['control']
        );
    }
}