<?PHP
namespace PaynetEasy\Paynet\Queries;

use PaynetEasy\Paynet\Responses\CardInfo;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfo extends AbstractQuery
{
    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct()
    {
        parent::__construct();

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
    public function createRequest($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->getOrder()->getRecurrentCard()->getData(),
            // Выделить этот код в отдельный класс
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
        return new CardInfo(parent::processResponse($response)->getArrayCopy());
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