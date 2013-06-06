<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\RecurrentCard;
use \PaynetEasy\Paynet\Responses\CardInfo;

use \PaynetEasy\Paynet\Exceptions\ConfigWrong;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfo extends Query
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

        $this->method       = 'get-card-info';
    }

    /**
     * @return \PaynetEasy\Paynet\Data\RecurrentCard
     */
    public function getRecurrentCard()
    {
        return $this->recurrent_card;
    }

    /**
     *
     * @param       RecurrentCard       $recurrent_card
     * @return      \PaynetEasy\Paynet\Queries\GetCardInfo
     */
    public function setRecurrentCard(RecurrentCard $recurrent_card)
    {
        $this->recurrent_card = $recurrent_card;

        return $this;
    }


    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigWrong('login undefined');
        }

        if(($this->recurrent_card instanceof RecurrentCard) === false)
        {
            throw new ConfigWrong('Order is not instance of Order');
        }

        $this->recurrent_card->validate();
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
            $this->recurrent_card->getData(),
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
            $this->recurrent_card->cardRefId().
            $this->config['control']
        );
    }
}