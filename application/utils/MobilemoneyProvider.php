<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("AbstractProviderRequest.php");

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class MobilemoneyProvider extends AbstractProviderRequest{

    protected $httpAdapter;
    protected $endpoint = [
        'live' => 'https://ericssonbasicapi1.azure-api.net/',
        'test' => 'https://sandbox.momodeveloper.mtn.com/',
    ];
    protected $baseUrl = "";
    protected $responseData;
    protected $configs = [];

    public function __construct($config){
        $this->httpAdapter = new GuzzleAdapter(null);
        $this->responseData = [];
        $this->configs = $config;
        $this->baseUrl = $this->endpoint[$config['mode']];
    }

    /**
     * Authorize to endpoint to get access token
     *
     * @param array $data authorization data
     * @return HttpResponse object
     */
    public function authorize(array $data = []){
        $auth_url = "https://proxy.momoapi.mtn.com/collection/"."token";
        $header = [
            'Authorization' => 'Basic '. base64_encode($this->configs['api_user'].':'.$this->configs['api_key']),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $this->configs['subscription-key']
		];
        $body = [
            "providerCallbackHost" => $this->configs['callback_url'] ?? '',
        ];
        $req = new Request('POST', $auth_url, $header, json_encode($body));
		return $this->httpAdapter->sendRequest($req);
    }

    public function purchase(array $data = []){
        $authResponse = $this->authorize($data); //get token
        $authData = json_decode($authResponse->getBody()->getContents(), true);

        if($authResponse->getStatusCode() == 200 ){
            $header = [
                'Authorization' => 'Bearer '. $authData['token'],
                'Content-Type' => "application/json",
                'Ocp-Apim-Subscription-Key' => $this->configs['subscription-key']
            ];
            $url = "https://proxy.momoapi.mtn.com/collection/v1_0/" . "requesttopay";
            $payload = [
                'amount' => $data['transaction_amount'] ?? '0',
                'currency' => $data['currency'] ?? 'CFA',
                'externalId' => '3324234',
                'payer' => [
                    'partyIdType' => "MSISDN",
                    'partyId' => $data['phone_number'] ?? 'CFA',
                ],
                'payerMessage' => 'Bill payment',
                'payeeNote' => 'Bill payment',
            ];
            $req = new Request('POST', $url, $header, json_encode($payload));
            $response = $this->httpAdapter->sendRequest($req);
            
            $data = json_decode($response->getBody(), true);
            $this->responseData =  [
                'status' => $response->getStatusCode(),
                'message' => $data['message']
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

    public function sandboxUser($configs){
        $url = "https://ericssonbasicapi1.azure-api.net/provisioning/v1_0/apiuser";
        $header = [
            'X-Reference-Id' => $this->generateUuidV4(),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $body = [
            "providerCallbackHost" => $configs['callback_url'] ?? '',
        ];
        $req = new Request('POST', $url, $header, json_encode($body));
        $response = $this->httpAdapter->sendRequest($req);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'user' => $response->getStatusCode() == 201 ? $header['X-Reference-Id'] : ''
            ]
        ];
    }
    
    public function sandboxApi($configs, $user){
        $url = "https://ericssonbasicapi1.azure-api.net/provisioning/v1_0/apiuser/".$user."/apikey";
        $header = [
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header);
        $response = $this->httpAdapter->sendRequest($req);
        $api = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'api-key' => $response->getStatusCode() == 201 ? $api['apiKey'] : ''
            ]
        ];
    }

    public function sandboxToken($configs){
        $url = "https://ericssonbasicapi1.azure-api.net/token";
        $header = [
            'Authorization' => 'Basic '. base64_encode($configs['api_user'].':'.$configs['api_key']),
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header);
        $response = $this->httpAdapter->sendRequest($req);
        $token = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'token' => $token['token']
            ]
        ];
    }
    
    public function sandboxPay($configs, $body){
        $url = "https://ericssonbasicapi1.azure-api.net/requesttopay";
        $header = [
            'Authorization' => 'Bearer '. $configs['token'],
            'Content-Type' => "application/json",
            'Ocp-Apim-Subscription-Key' => $configs['subscription-key']
		];
        $req = new Request('POST', $url, $header, json_encode($body));
        $response = $this->httpAdapter->sendRequest($req);
        $payment = json_decode($response->getBody()->getContents(), true);
        return [
            'status' => $response->getStatusCode(),
            'message' => $response->getBody()->getContents(),
            'data' => [
                'payment' => $payment
            ]
        ];
    }
}