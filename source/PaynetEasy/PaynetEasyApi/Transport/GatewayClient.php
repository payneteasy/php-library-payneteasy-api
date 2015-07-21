<?php
namespace PaynetEasy\PaynetEasyApi\Transport;

use PaynetEasy\PaynetEasyApi\Util\Validator;

use PaynetEasy\PaynetEasyApi\Transport\Response;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use PaynetEasy\PaynetEasyApi\Exception\RequestException;
use PaynetEasy\PaynetEasyApi\Exception\ResponseException;

class GatewayClient implements GatewayClientInterface
{
    /**
     * Curl options
     *
     * @var array
     */
    protected $curlOptions = array
    (
        CURLOPT_HEADER         => 0,
        CURLOPT_USERAGENT      => 'PaynetEasy-Client/1.0',
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST           => 1,
        CURLOPT_RETURNTRANSFER => 1
    );

    /**
     * {@inheritdoc}
     */
    public function makeRequest(Request $request)
    {
        $this->validateRequest($request);

        $responseString = $this->sendRequest($request);

        return $this->parseReponse($responseString);
    }

    /**
     * Executes request
     *
     * @param       Request         $request        Request to execute
     *
     * @return      string                          PaynetEasy response
     *
     * @throws      RequestException                Error while executing request
     */
    protected function sendRequest(Request $request)
    {
        $curl = curl_init($this->getUrl($request));

        curl_setopt_array($curl, $this->curlOptions);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request->getRequestFields()));

        $response = curl_exec($curl);

        if(curl_errno($curl))
        {
            $error_message  = 'Error occurred: ' . curl_error($curl);
            $error_code     = curl_errno($curl);
        }
        elseif(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
        {
            $error_code     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error_message  = "Error occurred. HTTP code: '{$error_code}'";
        }

        curl_close($curl);

        if (!empty($error_message))
        {
            throw new RequestException($error_message, $error_code);
        }
        else
        {
            return $response;
        }
    }

    /**
     * Parse PaynetEasy response from string to Response object
     *
     * @param       string      $response       PaynetEasy response as string
     *
     * @return      Response                    PaynetEasy response as object
     *
     * @throws      ResponseException           Error while parsing
     */
    protected function parseReponse($response)
    {
        if(empty($response))
        {
            throw new ResponseException('PaynetEasy response is empty');
        }

        $responseFields = array();

        parse_str($response, $responseFields);

        return new Response($responseFields);
    }

    /**
     * Validates Request
     *
     * @param       Request                 $request        Request for validation
     *
     * @throws      ValidationException                     Request data is invalid
     */
    protected function validateRequest(Request $request)
    {
        $validationErrors = array();

        if (strlen($request->getApiMethod()) == 0)
        {
            $validationErrors[] = 'Request api method is empty';
        }

        if (strlen($request->getEndPoint()) == 0 && strlen($request->getEndPointGroup()) === 0)
        {
            $validationErrors[] = 'Request end point is empty and request end point group is empty. Set one of them.';
        }

        if (strlen($request->getEndPoint()) > 0 && strlen($request->getEndPointGroup()) > 0)
        {
            $validationErrors[] = 'Request end point was set and request end point group was set. Set only one of them.';
        }

        if (count($request->getRequestFields()) === 0)
        {
            $validationErrors[] = 'Request data is empty';
        }

        if (!Validator::validateByRule($request->getGatewayUrl(), Validator::URL, false))
        {
            $validationErrors[] = 'Gateway url does not valid in Request';
        }

        if (!empty($validationErrors))
        {
            throw new ValidationException("Some Request fields are invalid:\n" .
                                          implode(";\n", $validationErrors));
        }
    }

    /**
     * Returns url for payment method, based on request data
     *
     * @param       Request         $request        Request for url creation
     *
     * @return      string      Url
     */
    protected function getUrl(Request $request)
    {
        if (strlen($request->getEndPointGroup()) > 0) {
            return "{$request->getGatewayUrl()}/{$request->getApiMethod()}/group/{$request->getEndPointGroup()}";
        }
        else
        {
            return "{$request->getGatewayUrl()}/{$request->getApiMethod()}/{$request->getEndPoint()}";
        }
    }
}