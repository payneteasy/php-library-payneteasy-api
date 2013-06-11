<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Data\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exceptions\ResponseException;
use PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class CreateCardRef extends AbstractQuery
{
    public function __construct()
    {
        parent::__construct();

        $this->method       = 'create-card-ref';
    }

    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(!$this->getOrder())
        {
            throw new ConfigException('Order is not defined');
        }

        $this->getOrder()->validateShort();
    }

    public function createRequest($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->getOrder()->getContextData(),
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode()
            )
        );

        return $this->wrapToRequest($query);
    }

    public function processResponse(Response $response)
    {
        if(!isset($response['card-ref-id']))
        {
            $e              = new ResponseException('card-ref-id undefined');
            $this->getOrder()->addError($e);
            $this->getOrder()->setState(OrderInterface::STATE_END);
            throw $e;
        }

        $response['cardrefid'] = $response['card-ref-id'];
        unset($response['card-ref-id']);

        return parent::processResponse($response);
    }

    protected function createControlCode()
    {
        // This is SHA-1 checksum of the concatenation
        // login + client-order-id + paynet-order-id + merchant-control.
        return sha1
        (
            $this->config['login'].
            $this->getOrder()->getOrderCode().
            $this->getOrder()->getPaynetOrderId().
            $this->config['control']
        );
    }
}