<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') OR exit('No direct script access allowed');
use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class Payments extends RestController {

	protected $httpAdapter;
	protected $baseUrl = "https://sandbox.momodeveloper.mtn.com/";

	public function __construct(){
		parent::__construct();
		$this->httpAdapter = new GuzzleAdapter(null);
	}

	/**
	 * Make a momo user creation request for API operations
	 *
	 * @return request status
	 */
	public function apiuser_post(){
		$header = [
            'Ocp-Apim-Subscription-Key' => $this->getHeader('ocp-apim-subscription-key'),
            'X-Reference-Id' => $this->guidv4(),
            'Content-Type' => "application/json"
		];
		//log_message('error', $header['Ocp-Apim-Subscription-Key']);
        $body = [
            "providerCallbackHost" => $this->getHeader("x-callback-url")
        ];
        $req = new Request('POST', $this->baseUrl."v1_0/apiuser", $header, json_encode($body));
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'data' => [
				'reference' => $header['X-Reference-Id'],
				'body' => json_decode($response->getBody(), true)
			]
		], $response->getStatusCode());
	}

	/**
	 * Return API user from momo portal
	 *
	 * @return mixed user info from momo portal if found, null otherwise
	 */
	public function apiuser_get(){
		$header = [
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key')
        ];
        $body = [
            "ref" => $this->getHeader("X-Reference-Id")
        ];
        $req = new Request('GET', $this->baseUrl."v1_0/apiuser/".$body['ref'], $header, '');
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'message' => $response->getStatusCode() == 200 ? "User info found!" : 'User info not found!',
			'user' => json_decode($response->getBody(), true)
		], $response->getStatusCode());
	}

	/**
	 * Generate API key for given user
	 *
	 * @return $APIKEY
	 */
	public function apikey_post(){
		$header = [
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key'),
            'Content-Type' => "application/json"
        ];
        $body = [
            "ref" => $this->getHeader("X-Reference-Id")
        ];
        $req = new Request('POST', $this->baseUrl."v1_0/apiuser/".$body['ref'].'/apikey', $header, '');
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'message' => $response->getStatusCode() == 201 ? "User API Key created!" : 'User API KEY not created!',
			'apiKey' => $response->getStatusCode() == 201 ? json_decode($response->getBody(), true)['apiKey'] : ''
		], $response->getStatusCode());
	}

	/**
	 * Request payment from momo user
	 *
	 * @return void
	 */
	public function momopay_post(){
		$header = [
            'Authorization' => 'Bearer '. ($this->getHeader('x-token')),
            'X-Callback-Url' => $this->getHeader('X-Callback-Url'),
            'X-Reference-Id' => $this->getHeader('X-Reference-Id'),
            'X-Target-Environment' => $this->getHeader('X-Target-Environment'),
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key'),
            'Content-Type' => "application/json"
		];
		$payer = $this->post("payer");
        $body = [
            "amount" => $this->post("amount"),
            "currency" => $this->post("currency"),
            "externalId" => $this->post("externalId"),
            "payer" => [
				'partyIdType' => $payer['partyType'],
				'partyId' => $payer['partyId']
			],
            "payerMessage" => $this->post("payerMessage"),
            "payeeNote" => $this->post("payeeNote")
        ];
        $req = new Request('POST', $this->baseUrl."collection/v1_0/requesttopay", $header, json_encode($body));
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'data' => $response->getStatusCode() == 202 ? "Payment request made to user. Awaiting confirmation" : json_decode($response->getBody(), true),
			'body' => $body
		], $response->getStatusCode());

	}

	/**
	 * Check status of a pending transaction
	 *
	 * @return $transaction
	 */
	public function checkpay_get(){
		$header = [
            'Authorization' => $this->getHeader('Authorization'),
            'X-Target-Environment' => $this->getHeader('X-Target-Environment'),
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key'),
            'Content-Type' => "application/json"
		];
		$reference = $this->get("referenceId");
        $req = new Request('GET', $this->baseUrl."collection/v1_0/requesttopay/".$reference, $header, '');
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'data' => json_decode($response->getBody(), true)
		], $response->getStatusCode());
	}
	
	/**
	 * Generate transaction or API call token
	 *
	 * @return API Token
	 */
	public function token_post(){
		$header = [
            'Authorization' => 'Basic '. base64_encode($this->getHeader('x-reference-id'). ":" . $this->getHeader('x-api-key')),
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key'),
            'Content-Type' => "application/json"
		];
        $req = new Request('POST', $this->baseUrl."collection/token/", $header);
		$response = $this->httpAdapter->sendRequest($req);
		
		
		$this->response([	
			'data' => json_decode($response->getBody(), true),
			'Authorization' => $response->getBody()->getContents(),
			'code' => $response->getStatusCode()
		], $response->getStatusCode());
	}
	
	/**
	 * Fetch user balance
	 *
	 * @return user balance
	 */
	public function accountbalance_get(){
		$header = [
            'Authorization' => 'Basic '. base64_encode($this->getHeader('x-reference-id'). ":" . $this->getHeader('x-api-key')),
			'X-Target-Environment' => $this->getHeader('X-Target-Environment'),
            'Ocp-Apim-Subscription-Key' => $this->getHeader('Ocp-Apim-Subscription-Key'),
            'Content-Type' => "application/json"
		];
        $req = new Request('GET', $this->baseUrl."collection/v1_0/account/balance", $header);
		$response = $this->httpAdapter->sendRequest($req);
		
		$this->response([
			'data' => json_decode($response->getBody(), true)
		], $response->getStatusCode());
	}

	private function getHeader($key = ''){
		return $this->_head_args[strtolower($key)];
	}

	private function guidv4(){
		if (function_exists('com_create_guid') === true)
			return trim(com_create_guid(), '{}');
	
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

}
