<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("ViewController.php");
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;


class Home extends ViewController {

	protected $httpAdapter;

	public function __construct(){
		parent::__construct('public/header', 'public/footer', 'public/body');
		$this->httpAdapter = new GuzzleAdapter(null);
	}

	/**
	 * Start the home page
	 */
	public function index(){
		$this->setBody('public/body')->loadView();
	}

}
