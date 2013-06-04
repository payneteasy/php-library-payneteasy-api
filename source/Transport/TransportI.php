<?PHP
namespace PaynetEasy\Paynet\Transport;

interface TransportI
{
    public function query($request);
}