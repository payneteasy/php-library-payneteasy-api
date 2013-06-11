<?PHP
namespace PaynetEasy\Paynet\Callbacks;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Queries\AbstractQuery;

class Redirect3D extends AbstractQuery
{
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

        return $this->wrapToRequest($data);
    }
}