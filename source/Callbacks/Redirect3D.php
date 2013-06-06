<?PHP
namespace PaynetEasy\Paynet\Callbacks;

use \PaynetEasy\Paynet\Queries\Query;

class Redirect3D extends Query
{
    /**
     * Control must be validated
     * @var boolean
     */
    protected $is_control = true;
    //
    // This class is empty, since the implementation of the Query does all the work
    //
}