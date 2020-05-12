<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("ViewController.php");

use chriskacerguis\RestServer\RestController;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;


class Gateway extends RestController {

	protected $httpAdapter;

	public function __construct(){
		parent::__construct('public/header', 'public/footer', 'public/body');
		$this->httpAdapter = new GuzzleAdapter(null);
		$this->load->model('providers');
	}

	public function gateways_get(){
		$providers = $this->providers->getAll()->result_object();
		$this->response([

		], $this->getRes);
	}

}
