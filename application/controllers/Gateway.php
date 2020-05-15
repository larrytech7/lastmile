<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (__DIR__."/../utils/EcobankProvider.php");
require_once("TransactionEvent.php");

use GuzzleHttp\Psr7\Request;
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

	//TODO : Setup gateeway configs here for other providers
	protected $gatewayConfig = [
		'subscription-key' => 'e7f1a6f931c74b019add9b3d018e9350', //momo
		'x-target-environment' => 'mtncameroon', //momo
		'userId' => 'iamaunifieddev103', //ecobank
		'password' => '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC', //ecobank
		'callback_url' => '' //general
	];

	public function __construct(){
		parent::__construct();
		
		$this->httpAdapter = new GuzzleAdapter(null);
		$ci_instance =& get_instance();
		$ci_instance->load->model('providers');
		$ci_instance->load->model('payments');
		$ci_instance->load->model('transactions');
		//initialize encryption library
		$ci_instance->encryption->initialize([
			'cipher' => 'aes-256'
		]);

		//register events
		//Events::register('eneopay_post_payments_event', [new TransactionEvent(), 'postPayments']);
		//Events::register('payments_callback_event', [new TransactionEvent(), 'postCallback']);
	}

	public function index_get(){
		redirect('http://52.174.179.186/payments-web/hostedPayment/payments', 'location', 301);
	}
	
	public function index_post(){
		redirect('http://52.174.179.186/payments-web/hostedPayment/payments', 'location', 301);
	}

	/**
	 * List all available active providers
	 * Save incoming transactions
	 * @return array list of active providers
	 */
	public function gateways_post(){
		$ext_transaction_id = $this->post('transaction_id');
		$callback_url = $this->post('return_url');
		$total = $this->post('total_amount');
		$data = $this->post('transactions');
		$transactions = [];
		if($data)
			foreach($data as $t){
				array_push($transactions,[
					'transaction_id' => $t['bill_ref'],
					'ext_transaction_id' => $ext_transaction_id,
					'transaction_amount' => $t['amount'],
				]);
			}
		if(count($transactions)>0)
		//save bills
		$this->transactions->insertBulk($transactions);

		$providers = $this->providers->getAll()->result_object();
		$this->response([
			'data' => $providers,
			'transaction_id' => $ext_transaction_id,
			'total' => $total,
			'return_url' => $callback_url
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
		$callback_url = $this->post('return_url');
		//TODO : Read transaction data from request, parse and insert
		$this->gatewayConfig['transaction_amount'] = $amount;
		//save pending payment
		$payment = [
			'payment_id' => time()+random_int(1,1000),
			'payment_transaction_id' => $transaction_id,
			'payment_amount' => $amount,
			'payment_provider' => $gateway,
			'payment_callback' => $callback_url,
			'payment_status' => 'PENDING'
		];
		$this->payments->insert($payment);
		$gatewayConfig['callback_url'] = site_url('gateway/callback').'?t_id='.$this->encryption->encrypt($payment['payment_transaction_id']);
		
		$providerGateway = new $this->paymentProviders[$gateway]; //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($this->gatewayConfig); //returns data from querying the actual provider
		
		$this->response([
			'status' => $response['status'],
			'message' => $response['message'],
			'isRedirect' => $providerGateway->isRedirect(),
			'redirect_url' => $providerGateway->getRedirectUrl(),
		], 
		$response['status'] ?? 401);
	}

	public function auth_get(){
		redirect('gateway/gateways');
	}

	/**
	 * Receive callback data from incoming payment requests and route it to the appropriate caller
	 *
	 * @return void
	 */
	public function callback_get(){
		$payment_status = ''; //TODO
		$transaction_id = $this->encryption->decrypt($this->get('t_id'));
		//var_dump($transaction_id);
		//retrieve the payment
		$payments = $this->payments->getWhere(['payment_transaction_id' => $transaction_id])->result_object();
		if(count($payments) > 0){
			//update payment
			$payment = $payments[0];
			$this->payments->update($payment->payment_id, [
				'payment_status' => $payment_status
			]);
			//get transactions
			$transactions = $this->transactions->getWhere(['ext_transaction_id' => $transaction_id])->result_object();
			foreach($transactions as &$tx){
				$tx['amount'] = $tx['transaction_amount'];
				$tx['bill_ref'] = $tx['transaction_id'];
			}
			//post payment to caller callback url
			$data = [
				'transactions' => $transactions,
				'transaction_id' => $transaction_id,
				'transaction_gateway' => $payment->provider_name,
				'transaction_amount' => $payment->provider_amount,
				'transaction_status' => $payment_status,
				'message' => '',
			];
			$req = new Request('POST', $payment->payment_callback, [], json_encode($data));
			$response = $this->httpAdapter->sendRequest($req);
			log_message('error', $response->getBody()->getContents());
			//TODO : post payment to Eneopay
		}

		//$event_post = Events::trigger('eneopay_post_payments_event', $payments[0], 'array');
		//$event_call = Events::trigger('payments_callback_event', $payments[0], 'array');
	}

	/**
	 * Add Payment provider
	 *
	 * @return void
	 */
	public function add_post(){
		$provider = [
			'provider_name' => $this->post('provider_name'),
			'provider_logo' => base_url('resources/flags/').$this->post('provider_logo'),
			'provider_short_tag' => $this->post('provider_tag'),
			'provider_status' => $this->post('provider_status'),
		];
		$added = $this->providers->insert($provider);
		$this->response([
			'message' => $added ? "Created" : "Error creating new provider",
			'status' => $added ?? false
		], $added ? RestController::HTTP_CREATED : RestController::HTTP_BAD_REQUEST);
	}

}
