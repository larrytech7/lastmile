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
		Events::register('eneopay_post_payments_event', ['TransactionEvent', 'postPayments']);
		Events::register('payments_callback_event', ['TransactionEvent', 'postCallback']);
	}

	public function index_get(){

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
		foreach($data as $t){
			array_push($transactions,[
				'transaction_id' => $t['bill_ref'],
				'ext_transaction_id' => $ext_transaction_id,
				'transaction_amount' => $t['amount'],
			]);
		}
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
		$transaction = [
			'transaction_amount' => $amount
		];
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
		$transaction['callback_url'] = site_url('gateway/callback').'?t_id='.$this->encryption->encrypt($payment['payment_transaction_id']);
		//TODO : Enter merchant's client details
		$transaction['userId'] = 'iamaunifieddev103';
		$transaction['password'] = '$2a$10$Wmame.Lh1FJDCB4JJIxtx.3SZT0dP2XlQWgj9Q5UAGcDLpB0yRYCC';

		$providerGateway = new $this->paymentProviders[$gateway]; //instantiates the right gateway according to the gateway code
		$response = $providerGateway->purchase($transaction); //returns data from querying the actual provider
		
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
		$payment_status = '';
		$transaction_id = $this->encryption->decrypt($this->get('t_id'));
		//var_dump($transaction_id);
		//retrieve the payment
		$payments = $this->payments->getWhere(['payment_transaction_id' => $transaction_id])->result_object();
		if(count($payments) > 0){
			//update payment
			$payment = $payments[0];
			$this->payments->update($payment->payment_id, [
				'payment_status' => ''
			]);
		}

		$event_post = Events::trigger('eneopay_post_payments_event', $payments[0], 'array');
		$event_call = Events::trigger('payments_callback_event', $payments[0], 'array');

	}

}
