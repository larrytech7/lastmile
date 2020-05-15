<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("ProviderInterface.php");

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class MobilemoneyProvider implements ProviderInterface{

    protected $httpAdapter;
    protected $endpoint = [
        'live' => '',
        'test' => 'https://sandbox.momodeveloper.mtn.com/',
    ];
    protected $baseUrl = "";
    protected $responseData;

    public function __construct(){
        $this->httpAdapter = new GuzzleAdapter(null);
        $this->responseData = [];
        $this->baseUrl = $this->endpoint['test']; //TODO Change when going live
    }

    /**
     * Authorize to endpoint to get access token
     *
     * @param array $data authorization data
     * @return HttpResponse object
     */
    public function authorize(array $data = []){
        $auth_url = $this->baseUrl."v1_0/apiuser";
        $header = [
            'X-Reference-Id' => $this->generateUuidV4(),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $data['subscription-key']
		];
        $body = [
            "providerCallbackHost" => $data['callback_url'] ?? '',
        ];
        $req = new Request('POST', $auth_url, $header, json_encode($body));
		return $this->httpAdapter->sendRequest($req);
    }

    public function purchase(array $data = []){
        $cardPaymentUrl = $this->baseUrl.'merchant/card';
        $authResponse = $this->authorize($data);
        $authData = json_decode($authResponse->getBody()->getContents(), true);

        if($authResponse->getStatusCode() == 200 ){
            $header = [
                'Authorization' => 'Bearer '. $authData['token'],
                'Origin' => 'developer.ecobank.com',
                'Content-Type' => "application/json",
                'Accept' => "application/json"
            ];
            //TODO : This configuration needs to be more dynamic
            $body = [
                "paymentDetails" => [
                    'requestID' => time(),
                    'productCode' => "ENP".random_int(1,1000),
                    'amount' => $data['transaction_amount'],
                    'currency' => $data['currency'] ?? 'CFA',
                    'locale' => 'en-US',
                    'orderInfo' => random_string(),
                    'returnUrl' => $data['callback_url'] ?? '',
                ],
                "merchantDetails" => [
                    'accessCode' => '2D726804',
                    'merchantID' => 'ECMT0001',
                    'secureSecret' => 'ENEO@ADMIN1',
                ],
                "secureHash" => '7f137705f4caa39dd691e771403430dd23d27aa53cefcb97217927312e77847bca6b8764f487ce5d1f6520fd7227e4d4c470c5d1e7455822c8ee95b10a0e9855'
            ];
            $req = new Request('POST', $cardPaymentUrl, $header, json_encode($body));
            $response = $this->httpAdapter->sendRequest($req);
            
            $data = json_decode($response->getBody(), true);
            $this->responseData =  [
                'status' => $response->getStatusCode(),
                'message' => $data['response_message'],
                'response_content' => $data['response_content'] 
            ];
            return $this->responseData;

        }else{
            return [
                'error' => 'Error ocurred with the request. Please try again',
                'status' => $authResponse->getStatusCode(),
                'message' => $authResponse->getBody()->getContents(),
            ];
        }
        
    }

    public function refund(array $data = []){
        
    }

    public function isRedirect(){
        return array_key_exists('response_content', $this->responseData);
    }

    public function getRedirectUrl(){
        return array_key_exists('response_content', $this->responseData) ? $this->responseData['response_content'] : "";
    }

    private function generateUuidV4(){
        // v4 generate uuid
        if (function_exists('com_create_guid') === true)
			return trim(com_create_guid(), '{}');
	
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}