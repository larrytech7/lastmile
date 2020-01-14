<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('ViewController.php');
use Social;

class Home extends ViewController {

	public function __construct(){
		parent::__construct('public/header', 'public/footer', 'public/body');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->setBody('public/body')->loadView();
	}

	/**
	 * Login user using any of the configured providers
	 * @param string $provider hte provider used to login with
	 */
	public function login($provider = 'manual'){
		switch($provider){
			case 'facebook':
				$this->login_facebook();
				break;
			case 'twitter':
				$this->login_twitter();
				break;
			case 'linkedin':
				$this->login_linkedin();
				break;
			case 'google':
				$this->login_google();
				break;
			default:
				$this->setBody('login')->loadView();
				break;
		}
	}

	private function login_twitter(){
		$client_id = $this->config->item('TWITTER_CLIENT_ID');
		$client_secret = $this->config->item('TWITTER_CLIENT_SECRET');
		$socialLogin = new Social();
		$response = $socialLogin->twitter_connect($client_id, $client_secret, site_url());
		if(!empty($response['redirectURL'])){
			//request requires a redirect
			redirect($response['redirectURL']);
		}else{
			//request completed
			if(!empty($response['id'])){
				//request completed successfully. Handle user data in $response
			}
		}
	}

	private function login_google(){
		$client_id = $this->config->item('GOOGLE_CLIENT_ID');
		$client_secret = $this->config->item('GOOGLE_CLIENT_SECRET');
		$api_key = $this->config->item('GOOGLE_API_KEY');
		$redirect_url = site_url('home/google');
		$socialLogin = new Social();
		$response = $socialLogin->gmail_connect($redirect_url, site_url(),$client_id, $client_secret, $api_key);
		if(!empty($response['redirectURL'])){
			//request requires a redirect
			redirect($response['redirectURL']);
		}else{
			//request completed
			if(!empty($response['email'])){
				//request completed successfully. Handle user data in $response
			}
		}

	}

	private function login_facebook(){
		$client_id = $this->config->item('FACEBOOK_APP_ID');
		$client_secret = $this->config->item('FACEBOOK_APP_SECRET');
		$scope = "public_profile, email, user_friends";
		$socialLogin = new Social();
		$response = $socialLogin->facebook_connect(NULL,$this->session, site_url(), $client_id, $client_secret, $scope);
		if(!empty($response['redirectURL'])){
			//request requires a redirect
			redirect($response['redirectURL']);
		}else{
			//request completed
			if(!empty($response['id'])){
				//request completed successfully. Handle user data in $response array
			}
		}
	}

	private function login_linkedin(){
		$client_id = $this->config->item('LINKEDIN_CLIENT_ID');
		$client_secret = $this->config->item('LINKEDIN_SECRET');
		$socialLogin = new Social();
		$response = $socialLogin->linkedin_connect(NULL, site_url(), $client_id, $client_secret);
		if(!empty($response['redirectURL'])){
			//request requires a redirect
			redirect($response['redirectURL']);
		}else{
			//request completed
			if(!empty($response['id'])){
				//request completed successfully. Handle user data in $response array
			}
		}
	}

}
