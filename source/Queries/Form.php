<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Transport\GatewayClientInterface;
use \PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class Form extends Sale
{
    /**
     * Constructor
     * @param       GatewayClientInterface      $transport      Transport
     * @param       boolean         $is_preauth     Preauch mode?
     */
    public function __construct(GatewayClientInterface $transport, $is_preauth = false)
    {
        parent::__construct($transport);

        $this->method       = $is_preauth ? 'preauth-form' : 'sale-form';
    }

    public function validate()
    {
        $this->validateConfig();

        if(!$this->getOrder())
        {
            throw new ConfigException('Order is not defined');
        }

        if(!$this->getOrder()->hasCustomer())
        {
            throw new ConfigException('Customer is not defined');
        }

        if($this->getOrder()->hasCreditCard())
        {
            throw new ConfigException('Credir Card must be undefined for Form API');
        }

        $this->getOrder()->validate();
        $this->getOrder()->getCustomer()->validate();
    }

    protected function initQuery()
    {
        return $this->sendQuery
        (
            array_merge
            (
                $this->getOrder()->getCustomer()->getData(),
                $this->getOrder()->getData(),
                $this->commonQueryOptions(),
                array
                (
                    '.method'       => $this->method,
                    '.end_point'    => $this->config['end_point']
                )
            )
        );
    }
}