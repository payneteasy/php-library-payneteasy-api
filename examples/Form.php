<?PHP
require_once './PaynetProcess.php';

use \PaynetEasy\Paynet\Data\Order;
use \PaynetEasy\Paynet\Data\Customer;
use \PaynetEasy\Paynet\Data\Card;

use \PaynetEasy\Paynet\Queries\Form         as PaynetForm;
use \PaynetEasy\Paynet\Queries\Status;
use \PaynetEasy\Paynet\Callbacks\Redirect3D;

use \PaynetEasy\Paynet\Responses\Response;

class Form extends Sale
{
    public function init()
    {
        parent::init();

        // for a credit card form should not be determined
        $this->card         = new \PaynetEasy\Paynet\Data\Data();
    }

    public function process_start()
    {
        // Step 1.
        // Create a query Sale
        $this->query            = new PaynetForm($this->transport);

        // Configurating it
        $this->query->setConfig($this->config);

        // Assign Query data
        $this->query->setCustomer($this->customer);
        $this->query->setOrder($this->order);

        // Step 2. Process query
        try
        {
            $this->process_response($this->query->process());
        }
        catch(\Exception $e)
        {
            $this->out_error($e);
        }
    }

}