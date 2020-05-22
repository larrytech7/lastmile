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

    public function __construct($config = []){
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
            "userId" => trim(preg_replace('/\s+/', '', $this->config['userId'])),
            "password" => trim(preg_replace('/\s+/', '',$this->config['password']))
        ];
        $req = new Request('POST', $auth_url, $header, json_encode($body));
		return $this->httpAdapter->sendRequest($req);
    }

    public function purchase(array $data = []){
        $cardPaymentUrl = $this->baseUrl.'merchant/card';
        $authResponse = $this->authorize($data);
        $authData = json_decode($authResponse->getBody()->getContents(), true);
        //log_message('error', 'Ecobank token result '.$authData['token']);

        if($authResponse->getStatusCode() == 200 ){
            $header = [
                'Authorization' => 'Bearer '. $authData['token'],
                'Origin' => 'developer.ecobank.com',
                'Content-Type' => "application/json",
                'Accept' => "application/json"
            ];
            //TODO : This configuration needs to be more dynamic
            $paymentDetails = [
                'requestId' => '4466',//time(),
                'productCode' => 'GMT112',//"ENP".random_int(1,1000),
                'amount' => $data['transaction_amount'],
                'currency' => 'GBP',//$data['currency'] ?? 'CFA',
                'locale' => 'en_AU',
                'orderInfo' => '255s353',//random_string(),
                'returnUrl' => $this->config['callback_url'] ?? ''
            ];
            $merchantDetails = [
                'accessCode' => '79742570',//'2D726804',
                'merchantID' => 'ETZ001',//'ECMT0001',
                'secureSecret' => 'sdsffd',//'ENEO@ADMIN1',
            ];
            $secureHash = $this->getSecureHash(array_merge($paymentDetails, $merchantDetails), $this->config['ecobank-api-key']);
            $body = [
                "paymentDetails" => $paymentDetails,
                "merchantDetails" => $merchantDetails,
                "secureHash" => '7f137705f4caa39dd691e771403430dd23d27aa53cefcb97217927312e77847bca6b8764f487ce5d1f6520fd7227e4d4c470c5d1e7455822c8ee95b10a0e9855'
            ];
            $req = new Request('POST', $cardPaymentUrl, $header, json_encode($body));
            $response = $this->httpAdapter->sendRequest($req);
            
            $data = json_decode($response->getBody(), true);
            $this->responseData = [
                'status' => $response->getStatusCode(),
                'message' => $data['response_message'],
                'response_content' => $data['response_content'],
                'secure_hash' => $secureHash 
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

    private function getSecureHash($payload, $lab_key){

        $message = '' ;
        foreach($payload as $key => $val){
            $message .= $val;
        }
        $message .= $lab_key;

        return hash("sha512", $message);
    }
}