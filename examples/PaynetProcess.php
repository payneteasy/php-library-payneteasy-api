<?PHP
require_once './config.php';
require_once './Template.php';
require_once './autoload.php';

use \PaynetEasy\Paynet\Transport\Curl;
use \PaynetEasy\Paynet\Data\Order;
use \PaynetEasy\Paynet\Data\Customer;
use \PaynetEasy\Paynet\Data\Card;
use \PaynetEasy\Paynet\Data\RecurrentCard;

use \PaynetEasy\Paynet\Queries\Sale;
use \PaynetEasy\Paynet\Queries\Status;
use \PaynetEasy\Paynet\Callbacks\Redirect3D;

use \PaynetEasy\Paynet\Responses\Response;

abstract class PaynetProcess
{
    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Curl
     */
    protected $transport;
    /**
     * @var string
     */
    protected $current_url;
    /**
     * @var Customer
     */
    protected $customer;
    /**
     * @var Card
     */
    protected $card;

    /**
     * @var RecurrentCard
     */
    protected $reccurent_card;

    /**
     * @var Order
     */
    protected $order;
    /**
     * @var Sale
     */
    protected $query;

    /**
     * Config for Paynet
     * @var array
     */
    protected $config;

    public function __construct($url)
    {
        $this->current_url      = $url;
        $this->template         = new Template();
    }

    public function init()
    {
        // Step 1.
        // Initialize the transport driver and configurated it.
        $this->transport        = new Curl(PAYNET_SERVER);

        // Step 2.
        // Make a order to be processed.
        $this->order            = new Order
        (
            array
            (
                'order_code'    => 'ORDER-'.strtoupper(uniqid()),
                'desc'          => 'This is test order',
                'amount'        => 1.99,
                'currency'      => 'RUB',
                'ipaddress'     => '127.0.0.1',
                'site_url'      => $this->current_url
            )
        );

        // Step 3.
        // Initialize the Customer data.
        $this->customer         = new Customer
        (
            array
            (
                'first_name'    => 'Joseph',
                'last_name'     => 'L. Doyle',
                'email'         => 'JosephLDoyle@example.com',
                'address'       => '2704 Colonial Drive',
                'birthday'      => '112681',
                'city'          => 'Houston',
                'state'         => 'TX',
                'zip_code'      => '1235',
                'country'       => 'US',
                'phone'         => '660-485-6353',
                'cell_phone'    => '660-485-6353'
            )
        );

        // Step 4.
        // Initialize the Credit Card data.
        $this->card             = new Card
        (
            array
            (
                'card_printed_name'         => 'Joseph Doyle',
                'credit_card_number'        => '4485 9408 2237 9130',
                'expire_month'              => '12',
                'expire_year'               => '14',
                'cvv2'                      => '321'
            )
        );

        $this->config           = array
        (
            'login'                         => PAYNET_LOGIN,
            'end_point'                     => PAYNET_END_POINT,
            'control'                       => PAYNET_CONTROL,
            'redirect_url'                  => $this->current_url.'&Redirect=1',
            'server_callback_url'           => $this->current_url
        );
    }

    /**
     * Response Handler for Paynet
     * @param Response $response
     */
    protected function process_response(Response $response)
    {
        // Step 3.
        // Handling response from paynet
        switch($this->query->state())
        {
            case Sale::STATE_PROCESSING:
            case Sale::STATE_WAIT:
            {
                $this->out_form_wait();
                break;
            }
            case Sale::STATE_REDIRECT:
            {
                $response->redirect();
                break;
            }
            case Sale::STATE_END:
            {
                $this->out_end($response);
                break;
            }
        }
    }

    public function out_form()
    {
        $this->template->content        = '<form method="post" action="" class="form-horizontal"><fieldset><legend>Sale Data</legend>';

        $this->template->content        .= '<div class="form-actions">
<button type="Submit" class="btn btn-large btn-primary" name="Process" value="">Process Sale</button>
<button class="btn btn-large">Reload form</button>
</div>';

        // Out all properties
        $this->out_properties
        (
            array_merge
            (
                $this->order->getData(),
                $this->customer->getData(),
                $this->card->getData()
            )
        );

        $this->template->content        .= '</fieldset></form>';

        $this->template->out();
    }

    protected function out_form_wait()
    {
        $this->template->content        = '<form method="post" action="" class="form-horizontal" id="form">
<fieldset><legend>Waiting...</legend>';

        $this->template->content        .= '<div class="progress progress-striped active">
<div class="bar" id="progress_bar" style="width: 0%;"></div></div>';

        $this->out_properties($this->order);

        $this->template->content        .= '<input type="hidden" name="Update" value="1"></input>';

        $this->template->content        .= '<div class="form-actions">
<button type="submit" class="btn btn-primary" name="UpdateButton" value="">Update</button></div>';

        $this->template->content        .= '</fieldset></form>';

        $this->template->content        .=  '<script type="text/javascript">
var total   = 5;
var time    = 5;
function progress_bar()
{

    $("#progress_bar").width((100 * (total-time) / total) + "%");

    time = time - 1;

    if(time >= 0)
	{
		var t = setTimeout("progress_bar()", 500);
		return;
	}
	clearTimeout(t);
	$("#form").submit();
}
$(document).ready(progress_bar);
</script>';

        $this->template->out();
    }

    protected function out_properties($properties)
    {
        foreach($properties as $key => $value)
        {
            $this->template->content    .= '
    <div class="control-group">
      <label class="control-label" for="'.$key.'">'.$key.'</label>
      <div class="controls">
        <input type="text" class="input-xlarge" id="'.$key.'" name="'.$key.'" value="'.htmlspecialchars($value).'">
      </div>
    </div>';
        }
    }

    protected function out_end(Response $response)
    {
        if($this->query->status()       === Sale::STATUS_APPROVED)
        {
            $this->template->content    = '<div class="alert alert-success"><h4 class="alert-heading">Approved!</h4>';
            $this->template->content    .= '<p>Congratulations! The transaction was approved.</p>';

            if($this->reccurent_card instanceof RecurrentCard)
            {
                $this->template->content    .= '<p><b>ReccurentCardId</b>: '.$this->reccurent_card->cardrefid().'</p>';
            }
        }
        elseif($this->query->status()   === Sale::STATUS_DECLINED)
        {
            $this->template->content    = '<div class="alert alert-block"><h4 class="alert-heading">Declined!</h4>';
            $this->template->content    .= '<p><b>Error Text</b>: '.$response->errorMessage().'</p>';
            $this->template->content    .= '<p><b>Error Code</b>: '.$response->errorCode().'</p>';
        }
        elseif($this->query->status()   === Sale::STATUS_ERROR)
        {
            $this->template->content    = '<div class="alert alert-error"><h4 class="alert-heading">Error!</h4>';
            $this->template->content    .= '<p><b>Error Text</b>: '.$this->query->getLastError()->getMessage().'</p>';
            $this->template->content    .= '<p><b>Error Code</b>: '.$this->query->getLastError()->getCode().'</p>';
        }
        else
        {
            $this->template->content    = '<div class="alert alert-error"><h4 class="alert-heading">Error!</h4>';
            $this->template->content    .= '<p>Status undefined</p>';
        }

        $this->template->content        .= '</div>';

        $this->template->out();
    }

    protected function out_error(\Exception $e)
    {
        $this->template->content        = '<div class="alert alert-error"><h3>An exception has occurred</h3>';
        $this->template->content       .= '<p>Text: '.  htmlspecialchars($e->getMessage()).'</p>';
        $this->template->content       .= '<p>Code: '.  htmlspecialchars($e->getCode()).'</p>';
        $this->template->content       .= '<p>Trace:</p>';
        $this->template->content       .= '<pre class="prettyprint"><code>'.$e->getTraceAsString().'</code></pre></div>';

        $this->template->out();
    }
}