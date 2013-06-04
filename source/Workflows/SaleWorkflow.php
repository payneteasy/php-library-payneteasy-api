<?PHP
namespace PaynetEasy\Paynet\Workflows;

use \PaynetEasy\Paynet\Data\Customer;
use \PaynetEasy\Paynet\Data\Card;

class SaleWorkflow
{
    /**
     * Domain for Paynet API
     * @var string
     */
    protected $paynet_server;

    /**
     * @var PaynetEasy\Paynet\Transport\Curl
     */
    protected $transport;



    public function __construct($config)
    {

    }

    public function setCustomer($customer)
    {

    }



}