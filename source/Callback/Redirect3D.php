<?PHP
namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Query\AbstractQuery;

class Redirect3D extends AbstractQuery
{
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }
    /**
     * Control must be validated
     * @var boolean
     */
    protected $is_control = true;

    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order, $data = null)
    {
        $this->validateOrder($order);

        $order->setState(OrderInterface::STATE_WAIT);

        return $this->wrapToRequest($data);
    }
}