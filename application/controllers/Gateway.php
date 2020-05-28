<?php
/**
 * @author Akah Harvey <l.akah@sevenadvancedacademy.com>
 * @copyright 2020 SevenGPS
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (__DIR__."/../utils/EcobankProvider.php");
require_once (__DIR__."/../utils/MobilemoneyProvider.php");
require_once (__DIR__."/../utils/OrangemoneyProvider.php");

require_once("TransactionEvent.php");

use GuzzleHttp\Psr7\Request;
use chriskacerguis\RestServer\RestController;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Client;

class Gateway extends RestController {

	protected $httpAdapter;

	protected $paymentProviders = [
		'mtnmomo' => MobilemoneyProvider::class,
		'orange' => OrangemoneyProvider::class,
		'ecobank' => EcobankProvider::class,
		'yup' => '',
		'eu' => '',
	];

	protected $deploymentConfig = [
		'app_callback_url' => [
			'live' => 'http://{live-env}/gateway/callback/', //url to process payment response from providers
			'test' => 'http://gateway-test.eneoapps.com/gateway/callback/',
			'dev' => 'http://192.168.100.10/payments/gateway/callback/'
		],
		'app_auth_url' => [
			'dev' => 'http://192.168.100.94/payments-web/#/hostedPayment/payments', //url for incoming app requests to authenticate and be redirected
			'test' => 'http://52.174.179.186/payments-web/#/hostedPayment/payments', //url for incoming app requests to authenticate and be redirected
			'live' => 'http://{live-env}/payments-web/#/hostedPayment/payments'
		],
		'app_status_url' => [
			'dev' => 'http://localhost:4200/payments-web/#/hostedPayment/payments/', //url for the front-end status update
			'test' => 'http://52.174.179.186/payments-web/#/hostedPayment/payments/', //url for the front-end status update
			'live' => 'http://{live-env}/payments-web/#/hostedPayment/payments/'
		],
		'app_notify_url' => [
			'dev' => 'http://192.168.100.10/payments/gateway/notify',
			'test' => 'http://gateway-test.eneoapps.com/gateway/gateway/notify',
			'live' => 'http://{live-env}/gateway/gateway/notify'
		],
		'app_eneopay_url' => [
			'dev' => 'http://192.168.100.17:8081/eneo-pay/',
			'test' => 'http://eneopay-test.eneoapps.com:8080/eneo-pay/',
			'live' => 'http://{live-env}/eneo-pay/'
		],
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
		$environment = 'dev';
		$this->app_auth_url = $this->deploymentConfig['app_auth_url'][$environment]; //change according to the deployment environment
		$this->app_callback_url = $this->deploymentConfig['app_callback_url'][$environment]; //change according to the deployment environment
		$this->app_status_url = $this->deploymentConfig['app_status_url'][$environment]; //change according to the deployment environment
		$this->app_notify_url = $this->deploymentConfig['app_notify_url'][$environment]; //change according to the deployment environment
		$this->app_eneopay_url = $this->deploymentConfig['app_eneopay_url'][$environment]; //change according to the deployment environment

		//register events
		//Events::register('eneopay_post_payments_event', [new TransactionEvent(), 'postPayments']);
		//Events::register('payments_callback_event', [new TransactionEvent(), 'postCallback']);
	}

	public function index_get(){
		redirect($this->app_auth_url);
	}
	
	public function index_post(){
		redirect($this->app_auth_url);
		//redirect('http://google.com');
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
		$data = $this->post('bills');
		//delete all bills before starting new transaction. @todo Remove this line when in production
		$this->transactions->clear();
		$bills = '';
		if($data)
			foreach($data as $t){
				log_message('error', 'bill_ref : '. $t['bill_ref']);
				$bills .= $t['bill_ref'] . ',';
				$this->transactions->insert([
					'transaction_id' => $t['bill_ref'],
					'ext_transaction_id' => $ext_transaction_id,
					'transaction_amount' => $t['amount'],
				]);
			}
		
		//initiate an EneoPay transaction request
		if($total > 0 && !empty($bills))
		$eneoPayment = $this->registerEneopay([
				'amount'=> $total, 
				'billNumbers' => trim($bills, ","),
				'paymentMethod' => 'MyEasyLightSite', //@todo Add payment method from application client
				'callbackUrl' => $this->app_notify_url,
				'transactionId' => $ext_transaction_id,
			]);
		log_message('error', 'Eneopay Register payment status '. $eneoPayment['status']);
		$message = '';
		$billArray = $eneoPayment['data'] ?? [] ;
		foreach($billArray as $val){ //in case of error, there's going to be a data field
			log_message('error', $val['billNumber'] . ' - ' . $val['status']);
			$message .= $val['status'] == 'Success' ? $val['billNumber'] : '';
		}

		$this->response([
			'status' => $eneoPayment['status'],
			'data' => ($eneoPayment['status'] == 200) ? $this->providers->getAll()->result_object() : [],
			'transaction_id' => $ext_transaction_id,
			'total' => $total,
			'message' => ($eneoPayment['status'] == 200) ? $eneoPayment['message'] : $eneoPayment['message'] . '. ' . $message,
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
		$this->gatewayConfig['callback_url'] = $this->app_callback_url . $gateway . '/'.(base64_encode(($transaction_id)));
		
		$providerGateway = new $this->paymentProviders[$gateway]($this->gatewayConfig); //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($data); //returns data from querying the actual provider
		log_message('error', 'encrypted ID request ' . base64_encode(($transaction_id)));
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
	public function paymentstatus_get($gateway, $transaction_id ){

		if($gateway == 'orange'){
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
				//update transaction status
				$processsedData = $this->processCallbackData($transaction_id, strtoupper($status));
				//return response
				$this->response([
					'status' => $status,
					'message' => $result['data']['confirmtxnmessage'],
					'data' => [
						'transactions' => $processsedData['transactions'] ?? '',
						'transaction_id' => $transaction_id,
						'transaction_amount' => $result['data']['amount'],
						'payment_transaction_id' => $result['data']['txnid'],
						'callback_url' => $result['data']['notifyUrl'],
					]
				], $code);
	
			}
		}else if ($gateway == 'mtnmo'){
			$this->response([
				'status' => 'FAILURE',
				'message' => '',
				'data' => []
			], 200);
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
		if(array_key_exists('transaction_status', $response) && in_array($response['transaction_status'], ['SUCCESS', 'PENDING'])){
			redirect($this->app_status_url . 'success?' . http_build_query($response));
		}else{ //response has error
			redirect($this->app_status_url . 'error?' . http_build_query($response));
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
				'payment_status' => $status == strtoupper('success') ? 'SUCCESS' :  ($status == strtoupper('pending') ? 'PENDING' : 'FAILED')
			]);
			//get transactions (bills)
			$transactions = $this->transactions->getWhere(['ext_transaction_id' => $transaction_id])->result_array();
			$transaction_ids = '';
			foreach($transactions as &$tx){
				$tx['amount'] = $tx['transaction_amount'];
				$tx['bill_ref'] = $tx['transaction_id'];
				$transaction_ids .= $tx['bill_ref'] . ',';
			} 
			//update eneopay about transaction
			$eneoUpdate = $this->updateEneopay([
				'status' => $status == strtoupper('success') ? 'Cash' : 'NotCash',
				'transactionId' => $transaction_id,
			]);
			$this->transactions->updateWhere(['ext_transaction_id' => $transaction_id] , ['transaction_status' => $status == strtoupper('success') ? 'SUCCESS' :  ($status == strtoupper('pending') ? 'PROCESSING' : 'FAILED')]);
			
			$callbackData = [
				'transaction_id' => $transaction_id,
				'transaction_gateway' => $payment->provider_name,
				'transaction_amount' => $payment->payment_amount,
				'transaction_status' => in_array($status, ['success', 'SUCCESS', 'OK', 'ok']) ? 'SUCCESS' : ($status == strtoupper('pending') ? 'PENDING' : 'FAILED'),
				'transactions' => $transaction_ids,
				'message' => in_array($status, ['success', 'SUCCESS', 'OK', 'ok' , 'Pending', 'PENDING', 'pending']) ? 'Payment completed for transaction : '.$transaction_id : $status,
				'callback' => $payment->payment_callback//. '?' . http_build_query($data)
			];
			log_message('error', sprintf('Eneopay Response. status : %s, message: %s. error %s.', $eneoUpdate['status'], $eneoUpdate['error'] , $eneoUpdate['message'] )); 
			
		}else{
			$message = 'Payment Transaction not found for '.$transaction_id;
			$callbackData = [
				'message' => $message,
				'error' => $message
			];
		}
		return $callbackData;
	}

	/**
	 * Register new bills transactions with EneoPay to ensure bills that have already been paid can no longer be paid
	 * Also pays bills that need to be paid
	 * 
	 * @param array $data
	 * @return array data from the request containing status and message about the transaction in eneopay
	 */
	private function registerEneopay($data){
		$username = 'seven-pay-client';
		$password = 'seven-pay-secret';
		$header['Authorization'] = 'Basic ' . base64_encode( $username . ':' . $password);		
		//$header['Content-Type'] = 'application/x-www-form-urlencoded';
	
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->app_eneopay_url
		]);
		$response = $client->request('POST', 'oauth/token', [
				'form_params' => [
					'username' => 'partner7',
					'password' => 'Seven@2020',
					'grant_type' => 'password',
					'client_id' => 'seven-pay-client',
				],
				'headers' => $header,
				'auth' => [$username, $password]
			]);
		$authData = json_decode($response->getBody()->getContents(), true);
		if($response->getStatusCode() == 200){
			$token = $authData['access_token'];
			log_message('error', ' Eneopay auth token ' . $token);
		}else{
			log_message('error', 'error getting token');
			return [
				'status' => $response->getStatusCode(),
				'error' => $response->getReasonPhrase()
			];
		}

		$updateReq = new Request('POST', $this->app_eneopay_url . 'bills/register_transaction', [
			'Authorization' => 'Bearer ' . $token,
			'Content-Type' => 'application/json'
		], json_encode($data));
		$updateResp = $this->httpAdapter->sendRequest($updateReq);
		$updateData = json_decode($updateResp->getBody()->getContents(), true);
		$logData = $updateData['data'] ?? [];
		array_walk($logData, function($key, $val){
			log_message('error', sprintf(' data %s %s', implode(',', $key), $val));
		});
		return [
			'status' => $updateResp->getStatusCode(),
			'message' => $updateData['message'],
			'data' => $updateData['data']
		];
	}
	
	/**
	 * Update Eneopay about the status of the payment made on gateway
	 * Eneopay is expected to update the state of the payment and return the status of the transaction
	 *
	 * @param array $data
	 * @return array data from the request containing status and message about the transaction state in Eneopay
	 */
	private function updateEneopay($data){
		$username = 'seven-pay-client';
		$password = 'seven-pay-secret';
		$header['Authorization'] = 'Basic ' . base64_encode( $username . ':' . $password);
	
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->app_eneopay_url
		]);
		$response = $client->request('POST', 'oauth/token', [
				'form_params' => [
					'username' => 'partner7',
					'password' => 'Seven@2020',
					'grant_type' => 'password',
					'client_id' => 'seven-pay-client',
				],
				'headers' => $header,
				'auth' => [$username, $password]
			]);
		$authData = json_decode($response->getBody()->getContents(), true);
		if($response->getStatusCode() == 200){
			$token = $authData['access_token'];
			log_message('error', ' Eneopay auth token ' . $token);
		}else{
			log_message('error', 'error getting token');
			return [
				'status' => $response->getStatusCode(),
				'error' => $response->getReasonPhrase()
			];
		}

		$updateReq = new Request('GET', $this->app_eneopay_url . 'bills/update_transaction?' . http_build_query($data), [
			'Authorization' => 'Bearer ' . $token,
			'Content-Type' => 'application/json'
		]);
		$updateResp = $this->httpAdapter->sendRequest($updateReq);
		$updateData = json_decode($updateResp->getBody()->getContents(), true);
		log_message('error', sprintf('status %s, message %s', $updateData['status'], $updateData['message']));

		return [
			'status' => $updateResp->getStatusCode(),
			'message' => $updateData['message'],
			'data' => $updateData['data']
		];
	}

	/**
	 * Notification callback to receive payment transaction updates from Eneopay
	 *
	 * @return void
	 */
	public function notify_post(){
		//get transaction details
		$transaction_id = $this->post('transactionId');
		$transaction_status = $this->post('status');
		$message = $this->post('message');
		$ip = $this->input->ip_address();
		$status = 0;
		if(in_array($ip, ['192.168.100.20', '52.174.179.186'])){
			$updated = $this->transactions->updateWhere(['ext_transaction_id' => $transaction_id] , ['transaction_status' => strtoupper($transaction_status)]);
			log_message('error', sprintf('Transaction update state for %s = %s . \n Reason : %s. $api %s', $transaction_id, $updated, $message, $ip));	
			$status = $updated ? RestController::HTTP_OK : RestController::HTTP_BAD_REQUEST;
		}else{
			$status = RestController::HTTP_UNAUTHORIZED;
		}
		log_message('error', sprintf('Callback ID %s, transaction_status : %s, message : %s, status : %s', $transaction_id, $transaction_status, $message, $status));
		$this->response(
			[
				'message' => $status == 200 ? 'ok' : 'error'
			],
			$status
		);
	}

	public function getApiKey_get($user = 0){
		$api_key = [
			'key' => str_rot13(sha1(time(), true)),
			'user_id' => $user == 0 ? time() : $user,
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
