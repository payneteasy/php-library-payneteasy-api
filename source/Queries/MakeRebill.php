<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\Order;
use \PaynetEasy\Paynet\Data\RecurrentCard;

use \PaynetEasy\Paynet\Transport\TransportI;

use \PaynetEasy\Paynet\Exceptions\ConfigWrong;

/**
 * The implementation of the query MakeRebill
 * http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class   MakeRebill           extends Sale
{
    /**
     * Reccurent Card
     * @var \PaynetEasy\Paynet\Data\RecurrentCard
     */
    protected $recurrent_card;

    /**
     * Comment
     * @var string
     */
    protected $comment      = '';

    /**
     * Constructor
     * @param       TransportI        $transport
     */
    public function __construct(TransportI $transport)
    {
        parent::__construct($transport);

        $this->method       = 'make-rebill';
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
     *
     * @return      \PaynetEasy\Paynet\Queries\MakeRebill
     */
    public function setRecurrentCard(RecurrentCard $recurrent_card)
    {
        $this->recurrent_card = $recurrent_card;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    /**
     *
     * @param string        $comment
     *
     * @return \PaynetEasy\Paynet\Queries\ReturnTransaction
     */
    public function setComment($comment)
    {
        $this->comment          = $comment;

        return $this;
    }

    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigWrong('login undefined');
        }

        if(($this->order instanceof Order) === false)
        {
            throw new ConfigWrong('Order is not instance of Order');
        }

        if(($this->recurrent_card instanceof RecurrentCard) === false)
        {
            throw new ConfigWrong('recurrent_card is not instance of RecurrentCard');
        }

        if(strlen($this->comment) > 50)
        {
            throw new ConfigWrong('comment is very big (over 50 chars)');
        }

        $this->order->validate();
        $this->recurrent_card->validate();
    }

    protected function init_query()
    {
        return $this->send_query
        (
            array_merge
            (
                $this->getOrder()->getData(),
                $this->recurrent_card->getData(),
                $this->common_query_options(),
                array
                (
                    'comment'       => $this->comment,
                    '.method'       => $this->method,
                    '.end_point'    => $this->config['end_point']
                )
            )
        );
    }

    protected function create_control_code()
    {
        return sha1
        (
            $this->config['end_point'].
            $this->order->getOrderCode().
            $this->order->getAmountInCents().
            $this->recurrent_card->cardrefid().
            $this->config['control']
        );
    }
}