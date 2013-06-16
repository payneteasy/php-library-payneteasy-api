<?PHP
namespace PaynetEasy\Paynet\Callback;

use PaynetEasy\Paynet\Query\AbstractQuery;

class ServerCallback extends AbstractQuery
{
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }
    //
    // This class is empty, since the implementation of the Query does all the work
    //
}

?>