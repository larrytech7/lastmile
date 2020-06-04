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
use GuzzleHttp\Client;

class EuProvider extends AbstractProviderRequest{

    protected $httpAdapter;
    protected $baseUrl = "http://195.24.207.114:9960/eumobile_api/v2.1/";
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
        $client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->baseUrl
		]);
		$response = $client->request('POST', '?service=sendPaymentRequest', [
				'form_params' => [
                    'hash' => $this->generateHash($this->config['eu-id'] . $this->config['eu-password'] . $data['transaction_id'] . $data['transaction_amount'] . 'XAF' . 
                        $data['name'] ?? '' . 
                        '237' . ($data['phone_number'] ?? '') . 
                        'Bill Payment EneoPay Gateway' . 
                        $this->config['eu-transaction-key']
                    ),
					'id' => $this->config['eu-id'] ,
					'pwd' => $this->config['eu-password'],
					'billno' => $data['transaction_id'], //@todo This is limited to 100 characters and may become a source of error if the transaction id becomes greater
					'amount' => $data['transaction_amount'],
					'currency' => 'XAF',
					'name' => $data['name'] ?? '',
					'phone' => '237'.  ($data['phone_number'] ?? ''),
					'label' => 'Bill Payment EneoPay Gateway',
				]
			]);
		$paymentResponseData = json_decode($response->getBody()->getContents(), true);
		if($response->getStatusCode() == 200){
			$token = $paymentResponseData['reference'];
            log_message('error', ' Eu payment request made ' . $token);
            $status = $paymentResponseData['statut'];
			$this->responseData = [
				'status' => $status == 100 ? 200 : $status+1,
				'error' => $status != 100 ? $paymentResponseData['message'] : '',
				'message' => $status == 100 ? $paymentResponseData['message'] : '',
				'data' => [
                    'phone_number' =>  $paymentResponseData['phone'],
                    'amount' =>  $paymentResponseData['amount'],
                    'reference' =>  $paymentResponseData['reference'],
                    'balance' =>  $paymentResponseData['balance'],
                    'commission' =>  $paymentResponseData['commission'],
                ],
			];
		}else{
			log_message('error', 'Eu payment request failed');
			$this->responseData = [
				'status' => $response->getStatusCode(),
				'message' => '',
				'error' => $response->getReasonPhrase()
			];
        }
        
        return $this->responseData;
        
    }

    public function refund(array $data = []){
        
    }

    private function generateHash($data){
        return md5($data);
    }

    public function isRedirect(){
        return false;
    }

    public function getRedirectUrl(){
        return array_key_exists('redirect_url', $this->responseData) ? $this->responseData['redirect_url'] : "";
    }
}