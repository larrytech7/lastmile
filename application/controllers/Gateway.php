<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (__DIR__."/../utils/EcobankProvider.php");
require_once (__DIR__."/../utils/MobilemoneyProvider.php");
require_once (__DIR__."/../utils/OrangemoneyProvider.php");

require_once("TransactionEvent.php");

use GuzzleHttp\Psr7\Request;
use chriskacerguis\RestServer\RestController;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class Gateway extends RestController {

	protected $httpAdapter;

	protected $paymentProviders = [
		'MTNMOMO' => MobilemoneyProvider::class,
		'ORANGEMO' => OrangemoneyProvider::class,
		'ECOBANK' => EcobankProvider::class,
		'YUP' => '',
		'EU' => '',
	];

	//TODO : Setup gateway configs here for all payment providers
	protected $gatewayConfig = [
		'subscription-key' => 'e7f1a6f931c74b019add9b3d018e9350', //momo
		'x-target-environment' => 'mtncameroon', //momo
		'api_key' => '', //momo
		'api_user' => '', //momo
		'orange-api-user' => 'Ndeme',//'MYEASYLIGTHPREPROD', //orangemo
		'orange-api-password' => 'Minipol88888',//'MYEASYLIGTHPREPROD2020', //orangemo
		'orange-consumer-key' => 'YsxQPIh775FPVM97OB7g9JLj3EMa',//'MYEASYLIGTHPREPROD2020', //orangemo
		'orange-consumer-secret' => 'IVUaxQ4zaOZz0rIOhJ43fviPNZoa',//'MYEASYLIGTHPREPROD2020', //orangemo
		'userId' => 'iamaunifieddev103', //ecobank
		'password' => '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC', //ecobank
		'ecobank-api-key' => '0C/5F7QHdMv40uVGaTbt5nXdJOxi105k2LN9goPRqTUrwZrdYOYbvC0sJz7G0iT9', //ecobank
		'callback_url' => '', //general
		'mode' => 'test' //general
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
		//redirect('http://52.174.179.186/payments-web/#/hostedPayment/payments', 'location', 301);
		redirect('http://google.com', 'location', 301);
		/* $momoProvider = new MobilemoneyProvider($this->gatewayConfig);
		$userData = $momoProvider->sandboxUser($this->gatewayConfig); //get api user
		if($userData['status'] == 201){ //get api key
			$user = $userData['data']['user'];
			$apiKeyData = $momoProvider->sandboxApi($this->gatewayConfig, $user);
			if($apiKeyData['status'] == 201){ //get token
				$apiKey = $apiKeyData['data']['api-key'];
				$this->gatewayConfig['api_user'] = $user;
				$this->gatewayConfig['api_key'] = $apiKey;
				$tokenData = $momoProvider->sandboxToken($this->gatewayConfig);
				if($tokenData['status'] == 200 ){ //make payment request
					$token = $tokenData['data']['token'];
					$this->gatewayConfig['token'] = $token;
					$payload = [
						'amount' => '100',
						'currency' => 'EUR',
						'externalId' => '3324234',
						'payer' => [
							'partyIdType' => "MSISDN",
							'partyId' => "678656032",
						],
						'payerMessage' => 'ok',
						'payeeNote' => 'ok',
					];
					$paymentData = $momoProvider->sandboxPay($this->gatewayConfig, $payload);
					var_dump($paymentData);
				}
			}
		} */
	}
	
	public function index_post(){
		//redirect('http://52.174.179.186/payments-web/#/hostedPayment/payments', 'location', 301);
		redirect('http://google.com', 'location', 301);
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
		$phone_number = $this->post('phone_number');
		$transaction_id = $this->post('transaction_id');
		$callback_url = $this->post('return_url');
		//data to pass to the payment processor
		$data['transaction_amount'] = $amount;
		$data['phone_number'] = $phone_number;
		$data['transaction_id'] = $transaction_id;
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
		$this->gatewayConfig['callback_url'] = site_url('gateway/callback').'/'.$this->encryption->encrypt($payment['payment_transaction_id']);
		
		$providerGateway = new $this->paymentProviders[$gateway]($this->gatewayConfig); //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($data); //returns data from querying the actual provider
		/* $this->response([
			'status' => $response->getStatusCode(),
		], 
		401); */
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
	 * @param string $id ID of the transaction of the payment initiated
	 * @return array response data
	 */
	public function callback_post($id){
		
		$request_data = file_get_contents("php://input");
		log_message('error', $request_data . ' ID '.$id);
		$transaction_id = $this->encryption->decrypt($id);
		$isCallbackSettled = false;
		$message = '';
		$payment_status = ''; //TODO
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

			$message = 'Payment completed for transaction : '.$transaction_id;
		}else{
			$message = 'Payment Transaction not found for '.$transaction_id;
		}

		$this->response([
			'status' => $isCallbackSettled,
			'message' => $message
		], $isCallbackSettled ? RestController::HTTP_OK : RestController::HTTP_BAD_REQUEST);

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
