<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\RecurrentCard;

use \PaynetEasy\Paynet\Transport\TransportI;

/**
 * The implementation of the Reccurent Transaction init
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions
 */
class   CreateRecurrentCard     extends Sale
{
    /**
     * Reccurent Card
     * @var \PaynetEasy\Paynet\Data\RecurrentCard
     */
    protected $recurrent_card;

    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(TransportI $transport)
    {
        parent::__construct($transport);

        $this->method       = 'sale';
    }

    /**
     * @return \PaynetEasy\Paynet\Data\RecurrentCard
     */
    public function getRecurrentCard()
    {
        return $this->recurrent_card;
    }

    public function process($data = null)
    {
        $response       = parent::process($data);

        if($response->isApproved())
        {
            $this->create_card_ref();
        }

        return      $response;
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

        $this->state        = $query->state();
        $this->status       = $query->status();
        $this->error        = $query->getLastError();

        if($e instanceof \Exception)
        {
            throw $e;
        }

        if($response->isApproved())
        {
            $this->recurrent_card   = new RecurrentCard($response['cardrefid']);
        }

        return $response;
    }
}