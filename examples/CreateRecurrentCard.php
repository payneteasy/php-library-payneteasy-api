<?PHP
require_once './Sale.php';

use \PaynetEasy\Paynet\Queries\Query;

use \PaynetEasy\Paynet\Data\RecurrentCard;
use \PaynetEasy\Paynet\Responses\Response;
use \PaynetEasy\Paynet\Queries\CreateCardRef;

use \Exception;

class   CreateRecurrentCard extends Sale
{
    protected function processResponse(Response $response)
    {
        if($this->query->state() === Query::STATE_END
        && $response->isApproved())
        {
            // Create Reccurent Card
            $this->createCardRef();
        }

        parent::process_response($response);
    }

    protected function createCardRef()
    {
        $query              = new CreateCardRef($this->transport);

        $query->setConfig($this->config);
        $query->setOrder($this->order);

        $e                  = null;
        try
        {
            /* @var $response \PaynetEasy\Paynet\Responses\Response */
            $response       = $query->process();
        }
        catch(Exception $e)
        {
        }

        if($response->isApproved())
        {
            $this->reccurent_card   = new RecurrentCard($response['cardrefid']);
        }

        return $response;
    }
}