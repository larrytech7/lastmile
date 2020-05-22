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
		'mtnmomo' => MobilemoneyProvider::class,
		'orange' => OrangemoneyProvider::class,
		'ecobank' => EcobankProvider::class,
		'yup' => '',
		'eu' => '',
	];

	//TODO : Setup gateway configs here for all payment providers
	protected $gatewayConfig = [
		'subscription-key' => 'e7f1a6f931c74b019add9b3d018e9350', //momo
		'x-target-environment' => 'mtncameroon', //momo
		'api_key' => '', //momo
		'api_user' => '', //momo
		'orange-api-user' => 'MYEASYLIGTHPREPROD', //orangemo
		'orange-api-password' => 'MYEASYLIGTHPREPROD2020', //orangemo
		'orange-consumer-key' => '4Qghk791gWiyWsYxJrwYAAsSTPsa',//orangemo
		'orange-consumer-secret' => 'I8f7pMZMahI2d2zvdkUjum0nu3Ua',//orangemo
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
		$ci_instance->load->model('apikey');
		//initialize encryption library
		$ci_instance->encryption->initialize([
			'cipher' => 'aes-256'
		]);

		//register events
		//Events::register('eneopay_post_payments_event', [new TransactionEvent(), 'postPayments']);
		//Events::register('payments_callback_event', [new TransactionEvent(), 'postCallback']);
	}

	public function index_get(){
		redirect('http://52.174.179.186/payments-web/#/hostedPayment/payments', 'location', 302);
		//redirect('http://google.com', 'location', 301);
	}
	
	public function index_post(){
		redirect('http://localhost:4200/#/hostedPayment/payments', 'location', 301);
		//redirect('http://google.com', 'location', 301);	
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
		$this->gatewayConfig['callback_url'] = 'http://gateway-test.eneoapps.come/gateway/gateway/callback/'. $gateway . '/'.(base64_encode(($transaction_id)));
		
		$providerGateway = new $this->paymentProviders[$gateway]($this->gatewayConfig); //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($data); //returns data from querying the actual provider
		log_message('error', 'encrypted ID request '.base64_encode(($transaction_id)));
		//die(var_dump($response));
		//return REST response
		$this->response([
			'status' => $response['status'],
			'message' => $response['message'],
			'data' => $response['data'] ?? [],
			'error' => $response['error'] ?? '',
			'isRedirect' => $providerGateway->isRedirect(),
			'redirect_url' => $providerGateway->getRedirectUrl(),
			'secure_hash' => $response['secure_hash']
		], 
		$response['status'] ?? 401);
	}

	/**
	 * Check for the payment status of the Orange payment request
	 *
	 * @param string $transaction_id
	 * @return void
	 */
	public function paymentstatus_get($transaction_id ){
		$paytoken = $this->get('paytoken');
		$auth_token = $this->get('auth-token');
		$x_token = $this->get('x-token');

		$headers[] = 'Authorization: ' . base64_decode($auth_token);
        $headers[] = 'X-AUTH-TOKEN: ' . base64_decode($x_token);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://apiw.orange.cm/'. "omcoreapis/1.0.2/mp/paymentstatus/" . base64_decode($paytoken));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$result = json_decode($response, true);
		die(var_dump($result));

		if($err){
			//return response
			$this->response([
				'status' => $code,	
				'message' => $result['message']
			], $code);
		}else{
			
			$status = $result['data']['status'];
			$this->payments->updateWhere([
				'payment_transaction_id' => $transaction_id
			], [
				'payment_status' => strtoupper($status)
			]);
			//return response
			$this->response([
				'status' => $status,
				'message' => $result['data']['confirmtxnmessage'],
				'data' => [
					'transaction_id' => $transaction_id,
					'transaction_amount' => $result['data']['amount'],
					'payment_transaction_id' => $result['data']['txnid'],
					'callback_url' => $result['data']['notifyUrl'],
				]
			], $code);

		}
	}

	public function auth_get(){
		redirect('gateway/gateways');
	}

	/**
	 * Receive callback data from incoming payment requests and route it to the appropriate caller
	 * @param string $id ID of the transaction of the payment initiated
	 * @return array response data
	 */
	public function callback_get($gateway, $id){
		//TODO : We need to parse calback  requests from payment providers
		$request_data = file_get_contents("php://input");
		$transaction_id = base64_decode($id);// ($this->encryption->decrypt(($id)));
		log_message('error', $request_data . 'encrypted ID Response : ' . $id . ' decrypted ID '.$transaction_id);
		$payment_status = '';
		//process callback
		switch($gateway){
			case 'ecobank': //process visa callback
				$payment_status = $this->get('vpc_Message') == 'Approved' ? 'SUCCESS' : strtoupper($this->get('vpc_Message'));
				$gateway_transaction_id = $this->get('vpc_TransactionNo'); ///TODO may be needed later
				log_message('error', 'Gateway transction id : ' . $gateway_transaction_id . ' Payment status : ' . $payment_status);
				break;
			case 'orange': //process orangemo callback

				break;
			case 'mtnmomo': //process momo callback

				break;
			case 'yup':
				break;
		}
		$response = $this->processCallbackData($transaction_id, $payment_status);
		if(array_key_exists('transaction_status', $response)){
			redirect('http://52.174.179.186/#/hostedPayment/payments/success'. '?' . http_build_query($response));
		}else{ //response has error
			redirect(' http://52.174.179.186/#/hostedPayment/payments/error');
		}
		//$event_post = Events::trigger('eneopay_post_payments_event', $payments[0], 'array');
		//$event_call = Events::trigger('payments_callback_event', $payments[0], 'array');
	}

	/**
	 * Process payment callbacks
	 *
	 * @param int $transaction_id
	 * @param string $status
	 * @return array Payment processing data to redirect to appropriate callback url
	 */
	private function processCallbackData($transaction_id, $status){
		///callback
		$callbackData = [];
		//retrieve the payment
		$payments = $this->payments->getWhere(['payment_transaction_id' => $transaction_id])->result_object();
		if(count($payments) > 0){
			//update payment
			$payment = $payments[0];
			$this->payments->update($payment->payment_id, [
				'payment_status' => $status
			]);
			//get transactions
			$transactions = $this->transactions->getWhere(['ext_transaction_id' => $transaction_id])->result_array();
			foreach($transactions as &$tx){
				$tx['amount'] = $tx['transaction_amount'];
				$tx['bill_ref'] = $tx['transaction_id'];
			}
			//post payment to caller callback url
			$data = [
				'transactions' => $transactions,
				'transaction_id' => $transaction_id,
				'transaction_gateway' => $payment->payment_provider,
				'transaction_amount' => $payment->payment_amount,
				'transaction_status' => $status,
				'message' => '',
			];
			/* $req = new Request('POST', $payment->payment_callback, [], json_encode($data));
			$response = $this->httpAdapter->sendRequest($req);
			log_message('error', $response->getBody()->getContents()); */
			log_message('error', 'Callback '.$payment->payment_callback); 
			$callbackData = [
				'transaction_id' => $transaction_id,
				'transaction_gateway' => $payment->provider_name,
				'transaction_amount' => $payment->payment_amount,
				'transaction_status' => in_array($status, ['success', 'SUCCESS', 'OK', 'ok']) ? 'SUCCESS' : $status,
				'message' => 'Payment completed for transaction : '.$transaction_id,
				'callback' => $payment->payment_callback//. '?' . http_build_query($data)
			];	
			//TODO : post payment to Eneopay
		}else{
			$message = 'Payment Transaction not found for '.$transaction_id;
			$callbackData = [
				'message' => $message
			];
		}
		return $callbackData;
	}

	public function getApiKey_get(){
		$api_key = [
			'key' => str_rot13(sha1(time(), true)),
			'user_id' => (time()),
			'level' => 1,
		];
		$inserted = $this->apikey->insert($api_key);
		$status = $inserted ? RestController::HTTP_OK : RestController::HTTP_BAD_REQUEST;
		$this->response([
			'status' => $status,
			'message' => $status == 200 ? 'Created!' : 'Error request parameters invalid',
			'data' => [
				'api_key' => $api_key['key']
			]
		], $status);
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
