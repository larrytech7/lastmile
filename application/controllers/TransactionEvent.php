<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;


class TransactionEvent extends CI_Controller{

	protected $httpAdapter;

	/**
	 * Constructor to build page views
	 */
	public function __construct(){
		parent::__construct();
		$this->load->model('providers');
		$this->load->model('payments');
		$this->load->model('transactions');
		$this->httpAdapter = new GuzzleAdapter(null);
	}

	/**
	 * Make request to post payments to Eneopay
	 *
	 * @param array $data - payments data to get the transactions from
	 * @return void
	 */
	public function postPayments($data){

	}
	
	/**
	 * Send data back to callback url
	 *
	 * @param array $data payments data to retrieve the appropriate transactions
	 * @return void
	 */
	public function postCallback($data){

	}


}
