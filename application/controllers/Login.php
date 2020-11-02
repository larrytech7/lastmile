<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("ViewController.php");
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class Login extends ViewController {

	protected $httpAdapter;

	public function __construct(){
		parent::__construct('public/header', 'public/footer', 'public/login');
		//$this->output->enable_profiler(true);
		$this->httpAdapter = new GuzzleAdapter(null);

		if($this->session->lang == NULL){
			$this->session->lang = 'fre';
		}
		if($this->session->isauth){
			redirect('admin');
		}
	}

	/**
	 * Start the home page
	 */
	public function index(){
		if($this->session->auth != NULL)
			redirect(site_url('admin'));

		$this->setBody('public/login')->loadView();
	}

	public function login(){
		//authenticate user
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		$user = $this->user->authenticate($email, $password);
		//log_message('error', sprintf('login user %s, email %s', $password, $email));
		$is_authenticated = password_verify($password, $user->result_object()[0]->password ?? '');

		if($is_authenticated){
			//authenticated successfully
			$this->session->set_flashdata('success', $this->lang->line('auth_success'));
			//setup session
			$user->result_object()[0]->password = null;
			$user = $user->result_object()[0];
			$settings = $this->setting->getAll()->result_object();
			$permissions = $this->user->getUserPermissions($user->roles_role_id)->result_object();
			$this->session->set_userdata(
				array(
					'user'=>$user,
					'tax_commissions'=>$settings,
					'permissions'=>$permissions,
					'auth' => true
					)
			);
			redirect(site_url('dashboard'), 'location');
		}else{
			$this->session->set_flashdata('error', $this->lang->line('auth_failure'));
			redirect(site_url(''), 'location');
		}
	}

	public function lang($lang){
		$this->session->lang = $lang;
		redirect(site_url(''), 'location');
	}

}
