<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("AbstractProviderRequest.php");

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class EcobankProvider extends AbstractProviderRequest{

    protected $httpAdapter;
    protected $baseUrl = "https://developer.ecobank.com/corporateapi/";
    protected $responseData;
    protected $config = [];

    public function __construct($config = ''){
        $this->httpAdapter = new GuzzleAdapter(null);
        $this->responseData = [];
        $this->config = $config;
    }

    public function authorize(array $data = []){
        $auth_url = $this->baseUrl."user/token";
        $header = [
            'Origin' => 'developer.ecobank.com',
            'Content-Type' => "application/json",
            'Accept' => "application/json"
		];
        $body = [
            "userId" => $this->config['userId'] ?? '',
            "password" => $this->config['password'] ?? ''
        ];
        $req = new Request('POST', $auth_url, $header, json_encode($body));
		return $this->httpAdapter->sendRequest($req);
    }

    public function purchase(array $data = []){
        $cardPaymentUrl = $this->baseUrl.'merchant/card';
        $authResponse = $this->authorize($data);
        $authData = json_decode($authResponse->getBody()->getContents(), true);
        log_message('error', 'token result'.$authResponse->getBody()->getContents());

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
                    'requestID' => '4466',//time(),
                    'productCode' => 'GMT112',//"ENP".random_int(1,1000),
                    'amount' => "50035",//$data['transaction_amount'],
                    'currency' => 'GBP',//$data['currency'] ?? 'CFA',
                    'locale' => 'en_AU',
                    'orderInfo' => '255s',//random_string(),
                    'returnUrl' => $this->config['callback_url'] ?? '',
                ],
                "merchantDetails" => [
                    'accessCode' => '79742570',//'2D726804',
                    'merchantID' => 'ETZ001',//'ECMT0001',
                    'secureSecret' => 'sdsffd',//'ENEO@ADMIN1',
                ],
                "secureHash" => '85dc50e24f6f36850f48390be3516c518acdc427c5c5113334c1c3f0ba122cdd37b06a10b82f7ddcbdade8d8ab92165e25ea4566f6f8a7e50f3c9609d8ececa4'
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
        return array_key_exists('response_content', $this->responseData);//TODO. This is insuffient to determine if there's a redirect url or not. Check that the content type is a web link before confirming
    }

    public function getRedirectUrl(){
        return array_key_exists('response_content', $this->responseData) ? $this->responseData['response_content'] : "";
    }

    private function getSecureHash($message){
        return hash("sha512", $message);
    }
}