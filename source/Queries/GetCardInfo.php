<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Responses\CardInfo;

use \PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfo extends Query
{
    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(GatewayClientInterface $transport)
    {
        parent::__construct($transport);

        $this->method       = 'get-card-info';
    }

    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(!$this->getOrder()->hasRecurrentCard())
        {
            throw new ConfigException('Order is not instance of Order');
        }

        $this->getOrder()->getRecurrentCard()->validate();
    }

    /**
     * Return CardInfo
     *
     * @return \PaynetEasy\Paynet\Responses\CardInfo
     */
    public function process($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->getOrder()->getRecurrentCard()->getData(),
            // Выделить этот код в отдельный класс
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode(),
                '.method'       => $this->method,
                '.end_point'    => $this->config['end_point']
            )
        );

        return new CardInfo($this->sendQuery($query)->getArrayCopy());
    }

    protected function createControlCode()
    {
        // This is SHA-1 checksum of the concatenation
        // login + cardrefid + merchant-control.
        return sha1
        (
            $this->config['login'].
            $this->getOrder()->getRecurrentCard()->cardRefId().
            $this->config['control']
        );
    }
}