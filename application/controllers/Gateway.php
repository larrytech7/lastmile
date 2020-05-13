<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once (__DIR__."/../utils/EcobankProvider.php");

use chriskacerguis\RestServer\RestController;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;


class Gateway extends RestController {

	protected $httpAdapter;

	protected $paymentProviders = [
		'MTNMOMO' => EcobankProvider::class,
		'ORANGEMO' => '',
		'ECOBANK' => EcobankProvider::class,
		'YUP' => '',
		'EU' => '',
	];

	public function __construct(){
		parent::__construct();
		//initialize encryption library
		$this->httpAdapter = new GuzzleAdapter(null);
		$ci_instance =& get_instance();
		$ci_instance->load->model('providers');
		$ci_instance->load->model('payments');
		$ci_instance->load->model('transactions');
	}

	/**
	 * List all available active providers
	 *
	 * @return array list of active providers
	 */
	public function gateways_get(){
		$providers = $this->providers->getAll()->result_object();
		$this->response([
			'data' => $providers
		], RestController::HTTP_OK);
	}

	/**
	 * Make a payment via the provider selected
	 *
	 * @return void
	 */
	public function makePayment_post(){
		$gateway = $this->post('gateway');
		$amount = $this->post('amount');
		$transaction_id = $this->post('transaction_id');
		//TODO : Read transaction data from request, parse and insert
		$transaction = [
			'transaction_id' => time()+random_int(1,1000),
			'transaction_amount' => $amount
		];
		//save transaction as pending
		$this->transactions->insert($transaction);
		//save pending payment
		$this->payments->insert([
			'payment_id' => time()+random_int(1,1000),
			'transaction_id' => $transaction_id,
			'transaction_amount' => $amount
		]);
		$transaction['callback_url'] = site_url('payments/gateway/callback');
		$transaction['userId'] = 'iamaunifieddev103';
		$transaction['password'] = '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC';

		$providerGateway = new $this->paymentProviders[$gateway]; //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($transaction); //returns data from querying the actual provider
		$this->response($response['data'], $response['status'] ?? 401);
	}

	public function auth_get(){
		redirect('gateway/gateways');
	}

	/**
	 * Receive callback data from incoming payment requests and route it to the appropriate caller
	 *
	 * @return void
	 */
	public function callback_post(){
		//decrypt request callback

	}

}
