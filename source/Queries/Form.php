<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Transport\TransportI;

use \PaynetEasy\Paynet\Data\Customer;
use \PaynetEasy\Paynet\Data\Order;
use \PaynetEasy\Paynet\Data\Card;

use \PaynetEasy\Paynet\Exceptions\ConfigWrong;

/**
 * The implementation of the query Form
 * http://wiki.payneteasy.com/index.php?title=PnE%3APayment_Form_integration
 */
class Form extends Sale
{
    /**
     * Constructor
     * @param       TransportI      $transport      Transport
     * @param       boolean         $is_preauth     Preauch mode?
     */
    public function __construct(TransportI $transport, $is_preauth = false)
    {
        parent::__construct($transport);

        $this->method       = $is_preauth ? 'preauth-form' : 'sale-form';
    }

    public function validate()
    {
        $this->validateConfig();

        if(($this->customer instanceof Customer) === false)
        {
            throw new ConfigWrong('Customer is not instance of Customer');
        }

        if(($this->order instanceof Order) === false)
        {
            throw new ConfigWrong('Order is not instance of Order');
        }

        if($this->card instanceof Card)
        {
            throw new ConfigWrong('Credir Card must be undefined for Form API');
        }

        $this->customer->validate();
        $this->order->validate();
    }

    protected function initQuery()
    {
        return $this->sendQuery
        (
            array_merge
            (
                $this->getCustomer()->getData(),
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