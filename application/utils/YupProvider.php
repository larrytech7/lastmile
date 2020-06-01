<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Date : 28/05/2020
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("AbstractProviderRequest.php");

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class YupProvider extends AbstractProviderRequest{

    protected $httpAdapter;
    protected $baseUrl = "https://fr.tagpay.com";
    protected $responseData;

    public function __construct($config){
        $this->httpAdapter = new GuzzleAdapter(null);
        $this->responseData = [];
        $this->config = $config;
    }

    public function authorize(array $data = []){
        $auth_url = $this->baseUrl . "/online/online.php?" . http_build_query(['merchantid' => $this->config['yup-merchant-id']]);
        
        $req = new Request('GET', $auth_url , []);
		return $this->httpAdapter->sendRequest($req);
    }

    public function purchase(array $data = []){
        $paymentUrl = $this->baseUrl . '/online/online.php';
        $authResponse = '';//$this->authorize($data);
        $authData = [];//json_decode($authResponse->getBody()->getContents(), true);

        //@todo Remove this section when values are made available
        $this->responseData =  [
            'sessionid' => 123,
            'status' => 200,
            'data' => [
                'sessionid' => 1234,
                'merchantid' => $this->config['yup-merchant-id'],
                'amount' => $data['transaction_amount'],
                'currency' => $data['currency'] ?? 'CFA',
                'purchaseref' => $data['transaction_id'] ?? 'CFA',
                'phonenumber' => $data['phone_number'] ?? 'CFA',
                'language' => 'en',
                'brand' => 'ENEOPAY GATEWAY',
                'description' => 'Eneo Payments',
                'accepturl' => $data['callback_url'] ?? '',
                'declineurl' => $data['callback_url'] ?? '',
                'cancelurl' => $data['callback_url'] ?? '',
                "text" => ''
            ],
            'message' => 'ok',
            'redirect_url' => $paymentUrl 
        ];
        return $this->responseData;
        /* if($authResponse->getStatusCode() == 200 ){
        
            //@todo : This configuration needs to be more dynamic
            $data = [
                'sessionid' => $authData['sessionID'],
                'merchantid' => $this->config['yup-merchant-id'],
                'amount' => $data['transaction_amount'],
                'currency' => $data['currency'] ?? 'CFA',
                'purchaseref' => $data['transaction_id'] ?? 'CFA',
                'phonenumber' => $data['phone_number'] ?? 'CFA',
                'language' => 'en',
                'brand' => 'ENEOPAY GATEWAY',
                'description' => 'Eneo Payments',
                'accepturl' => $data['callback_url'] ?? '',
                'declineurl' => $data['callback_url'] ?? '',
                'cancelurl' => $data['callback_url'] ?? '',
                "text" => ''
            ];

            $this->responseData =  [
                'sessionid' => $authData['sessionID'],
                'status' => $authResponse->getStatusCode(),
                'data' => $data,
                'message' => $data['response_message'],
                'redirect_url' => $paymentUrl 
            ];
            return $this->responseData;

        }else{
            return [
                'error' => 'Error ocurred with the request. Please try again',
                'status' => $authResponse->getStatusCode(),
                'message' => $authResponse->getBody()->getContents(),
            ];
        } */
        
    }

    public function refund(array $data = []){
        
    }

    public function isRedirect(){
        return array_key_exists('sessionid', $this->responseData) && !empty($this->responseData['sessionid']);
    }

    public function getRedirectUrl(){
        return array_key_exists('redirect_url', $this->responseData) ? $this->responseData['redirect_url'] : "";
    }
}