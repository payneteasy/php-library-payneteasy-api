<?PHP
namespace PaynetEasy\Paynet\Callbacks;

use PaynetEasy\Paynet\Queries\AbstractQuery;

class Redirect3D extends AbstractQuery
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