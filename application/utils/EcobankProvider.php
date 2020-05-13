<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('ProviderInterface.php');
use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class EcobankProvider implements ProviderInterface{

    protected $httpAdapter;
	protected $baseUrl = "https://developer.ecobank.com/corporateapi/";

    public function __construct(){
        $this->httpAdapter = new GuzzleAdapter(null);
    }

    public function authorize(array $data = []){
        $auth_url = $this->baseUrl."user/token";
        $header = [
            'Origin' => 'developer.ecobank.com',
            'Content-Type' => "application/json",
            'Accept' => "application/json"
		];
        $body = [
            "userId" => $data['userId'] ?? '',
            "password" => $data['password'] ?? ''
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
                "secureHash" => ''
            ];
            $req = new Request('POST', $cardPaymentUrl, $header, json_encode($body));
            $response = $this->httpAdapter->sendRequest($req);
            
            return [
                'message' => $response->getStatusCode() == 202 ? "Payment request made to user. Awaiting confirmation" : json_decode($response->getBody(), true),
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode()
            ];
        }else{
            return [
                'data' => '',
                'status' => $response->getStatusCode(),
                'message' => $response->getBody()->getContents(),
            ];
        }
        
    }
}