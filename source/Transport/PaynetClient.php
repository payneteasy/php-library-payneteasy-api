<?PHP
namespace PaynetEasy\Paynet\Transport;

use \PaynetEasy\Paynet\Exceptions\ConfigException;
use \PaynetEasy\Paynet\Exceptions\RequestException;
use \PaynetEasy\Paynet\Responses\Response;
use \Exception;

class PaynetClient implements TransportI
{
    protected $server;

    protected $base_url;

    /**
     * Curl driver
     * @param       string      $server     Domain
     * @param       string      $base_url   Base Url
     */
    public function __construct($server, $base_url = '/paynet/api/v2/')
    {
        $this->server   = $server;

        $this->base_url = $base_url;
    }

    public function query($request)
    {
        if(empty($request['.method']))
        {
            throw new ConfigException('.method not defined in paynet request');
        }

        if(empty($request['.end_point']))
        {
            throw new ConfigException('.end_point not defined in paynet request');
        }

        $method             = $request['.method'];
        $end_point          = $request['.end_point'];

        unset($request['.method']);
        unset($request['.end_point']);

        $e                  = null;
        try
        {
            // Реальная отправка запроса
            $curl           = curl_init();

            $user_agent     = 'PaynetEasy-Client/1.0';

            if(isset($request['user_agent']))
            {
                $user_agent = $request['user_agent'];
            }

            $url            = 'https://'.$this->server.$this->base_url.$method.'/'.$end_point;

            curl_setopt_array
            (
                $curl,
                array
                (
                    CURLOPT_URL            => $url,
                    CURLOPT_HEADER         => 0,
                    CURLOPT_USERAGENT      => $user_agent,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_POST           => 1,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POSTFIELDS     => http_build_query($request)
                )
            );

            $response       = curl_exec($curl);

            if(curl_errno($curl))
            {
                throw new RequestException(curl_error($curl), curl_errno($curl));
            }

            if(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
            {
                throw new RequestException
                (
                    sprintf
                    (
                    	'response http error code "%s"',
                         curl_getinfo($curl, CURLINFO_HTTP_CODE)
                    )
                );
            }

            if(empty($response))
            {
                throw new RequestException('response is empty');
            }

            $result             = array();

            parse_str($response, $result);

            if(empty($result))
            {
                throw new RequestException('Response parsing error: '.$response);
            }

            $response           = new Response($result);
        }
        catch (Exception $e)
        {
        }

        // Close the resource
        // (finally imitation)
        curl_close($curl);

        // If excaption has occured send its
        if(!empty($e))
        {
            throw $e;
        }

        return $response;
    }
}