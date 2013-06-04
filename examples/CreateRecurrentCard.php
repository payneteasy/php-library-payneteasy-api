<?PHP
require_once './Sale.php';

use \PaynetEasy\Paynet\Queries\Query;

use \PaynetEasy\Paynet\Data\RecurrentCard;
use \PaynetEasy\Paynet\Responses\Response;
use \PaynetEasy\Paynet\Queries\CreateCardRef;

class   CreateRecurrentCard extends Sale
{
    protected function process_response(Response $response)
    {
        if($this->query->state() === Query::STATE_END
        && $response->isApproved())
        {
            // Create Reccurent Card
            $this->create_card_ref();
        }

        parent::process_response($response);
    }

    protected function create_card_ref()
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
        catch(\Exception $e)
        {
        }

        if($response->isApproved())
        {
            $this->reccurent_card   = new RecurrentCard($response['cardrefid']);
        }

        return $response;
    }
}