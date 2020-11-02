<?php

/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 18/08/2020
 * Contributors : zeufack
 */

defined('BASEPATH') or exit('No direct script access allowed');
require_once("ViewController.php");

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Client;

class Dashboard extends ViewController
{

	protected $httpAdapter;
	protected $requestClient;
	protected $base_url;
	protected $headers;
	protected $request_body;

	public function __construct()
	{
		parent::__construct('admin/header', 'admin/footer', 'admin/dashboard');
		if (ENVIRONMENT == 'development')
			$this->output->enable_profiler(true);

		$this->httpAdapter = new GuzzleAdapter(null);
		$this->base_url = site_url('cafe');
		$this->requestClient = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->base_url
		]);
		$this->load->helper(array('form', 'url'));

		if ($this->session->auth == NULL) {
			redirect(site_url('login'), 'location');
		}
		//set request headers
		$this->headers = [
			'Authorization' => 'Basic ' . base64_encode($this->config->item('api_user') . ':' . $this->config->item('api_password')),
			'x-api-key' => $this->config->item('api_key')
		];
		$this->request_body = [
			'user_id' => $this->session->user->user_id,
			'user_role' => $this->session->user->role_id
		];

		//fetch global stats
		$stats_request = new Request('GET', $this->base_url . '/stats?' . http_build_query($this->request_body), $this->headers);
		$franchise_request = new Request('GET', $this->base_url . '/users?' . http_build_query($this->request_body), $this->headers);
		try {
			$response = $this->httpAdapter->sendRequest($stats_request);
			$stats = json_decode($response->getBody()->getContents(), true);
			$franchise_response = $this->httpAdapter->sendRequest($franchise_request);
			$franchises = json_decode($franchise_response->getBody()->getContents(), true);

			$this->setData([
				'statistics' => $stats['data'] ?? [],
				'franchises' => $franchises['data']['users']
			]);
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
	}

	private function init_rbac()
	{
		$path = uri_string();
		$item = explode('/', $path)[1] ?? '';
		if ($this->session->user->role_name != 'ADMIN' && in_array($item, ['users', 'franchises', 'settings'])) {
			$this->session->set_flashdata('error', $this->lang->line('access_denied'));
			redirect(site_url('dashboard'));
		}
		//log_message('error', 'split path ' . $item);
	}

	/**
	 * Admin dashboard page
	 */
	public function index()
	{
		$this->session->set_flashdata('franchise_menu', 'active');
		$this->setBody('admin/dashboard')->loadView();
	}

	/**
	 * List all franchises
	 *
	 * @return void
	 */
	public function franchises()
	{
		$this->init_rbac();
		$this->session->set_flashdata('franchise_menu', 'active');
		$this->setBody('admin/dashboard')->loadView();
	}

	public function franchise_add()
	{
		$franchise['username'] = $this->input->post('username');
		$franchise['name'] = $this->input->post('name');
		$franchise['email'] = $this->input->post('email');
		$franchise['user_address'] = $this->input->post('address');
		$franchise['user_phone_number'] = $this->input->post('phone_number');
		$franchise['status'] = 'ACTIVE';
		$franchise['role'] = 2;
		$franchise['password'] = random_string();
		//log_message('error', 'user data. ' . implode('=', $franchise));
		//save franchise
		$requestClient = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->base_url
		]);
		try {
			//$response = $this->httpAdapter->sendRequest($request);
			$response = $requestClient->request('PUT', '/cafe-referral/cafe/user', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ($franchise)
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
				$this->session->set_flashdata('data', $data['data']['user'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating franchise. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}

		redirect(site_url('dashboard'));
	}

	public function user_add()
	{
		$users['username'] = $this->input->post('username');
		$users['name'] = $this->input->post('name');
		$users['email'] = $this->input->post('email');
		$users['user_address'] = $this->input->post('address');
		$users['user_phone_number'] = $this->input->post('phone_number');
		$users['status'] = 'ACTIVE';
		$users['role'] = 1;
		$users['password'] = random_string();
		//log_message('error', 'user data. ' . implode('=', $franchise));
		//save user
		try {
			//$response = $this->httpAdapter->sendRequest($request);
			$response = $this->requestClient->request('PUT', '/cafe-referral/cafe/user', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ($users)
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
				$this->session->set_flashdata('data', $data['data']['user'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}

		redirect(site_url('dashboard/users'));
	}

	public function user($action, $user_id)
	{
		$this->session->set_flashdata('user_menu', 'active');

		if ($action == 'delete') {
			try {
				$response = $this->requestClient->request('DELETE', '/cafe-referral/cafe/user', [
					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => ['user_id' => $user_id]
				]);

				$data = json_decode($response->getBody()->getContents(), true);

				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->session->set_flashdata('success', $data['message'] ?? '');
					$this->session->set_flashdata('data', $data['data']['user'] ?? '');
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
				//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
			} catch (RequestException $ex) {
				log_message('error', 'Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}
			redirect(site_url('dashboard'));
		} else {
			if ($action == "edit") {
				//set form validation rules
				$this->form_validation->set_rules('email', $this->lang->line('email'), 'required|min_length[4]|valid_email', [
					'valid_email' => $this->lang->line('invalid_email'),
					'required' => $this->lang->line('email_required'),
					'min_length' => $this->lang->line('email_length')
				]);
				$this->form_validation->set_rules('password', $this->lang->line('password'), 'callback_password_update_check');

				//update user
				if ($this->form_validation->run() == true) {
					try {
						$user['name'] = $this->input->post('name');
						$user['username'] = $this->input->post('username');
						$user['email'] = $this->input->post('email');
						$user['user_phone_number'] = $this->input->post('phone_number');
						$user['user_address'] = $this->input->post('user_address');
						$user['status'] = $this->input->post('status');
						$user['user_id'] = $user_id;
						$password = $this->input->post('password');
						if (strlen($password) > 0) {
							$user['password'] = $password;
						}

						$response = $this->requestClient->request('POST', '/cafe-referral/cafe/user', [
							'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
							'headers' => $this->headers,
							'json' => $user
						]);

						$data = json_decode($response->getBody()->getContents(), true);

						if ($response->getStatusCode() == 200) {
							$this->setData([
								'user' => $data['data']['user'][0] ?? []
							]);
							$this->session->set_flashdata('success', $data['message'] ?? '');
						} else {
							$this->session->set_flashdata('error', $data['message'] ?? '');
						}
						log_message('error', 'update error. ' . implode('-', $user));
					} catch (RequestException $ex) {
						log_message('error', 'Error updating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					} catch (Exception $ex) {
						log_message('error', 'Generic Error updating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					}
				} else {
					$message = stripcslashes(stripslashes(html_entity_decode(strip_tags(validation_errors()))));
					$this->session->set_flashdata('error', $this->lang->line('failure') . ' . ' . $message);
					//$this->setBody('admin/user')->loadView();
				}
				redirect(site_url('dashboard/user/view/' . $user_id), 'location');
				//redirect(site_url('dashboard'));
			} else {
				//view user
				try {
					$response = $this->requestClient->request('GET', '/cafe-referral/cafe/user?' . http_build_query(['user' => $user_id]), [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers,
						'form_params' => ['user_id' => $user_id]
					]);

					$data = json_decode($response->getBody()->getContents(), true);

					if ($data['status'] == 'success') {
						//@todo Send email to the user about created account
						$this->setData([
							'user' => $data['data']['user'][0] ?? []
						]);
					} else {
						$this->session->set_flashdata('error', $data['message'] ?? '');
					}
					//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
				} catch (RequestException $ex) {
					log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} catch (Exception $ex) {
					log_message('error', 'Generic Error creating franchise. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				}

				$this->setBody('admin/user')->loadView();
			}
		}
	}

	/**
	 * list users of the system
	 *
	 * @return void
	 */
	public function users()
	{
		$this->init_rbac();
		$this->session->set_flashdata('user_menu', 'active');
		//redirect user to home page if not admin
		$users_request = new Request('GET', $this->base_url . '/users?' . http_build_query($this->request_body), $this->headers);
		try {
			$response = $this->httpAdapter->sendRequest($users_request);
			$users = json_decode($response->getBody()->getContents(), true);

			$this->setData([
				'users' => $users['data']['users']
			]);
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/users')->loadView();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function client($action, $client_id)
	{
		$this->init_rbac();
		$this->session->set_flashdata('client_menu', 'active');

		if ($action == 'delete') {
			try {
				$response = $this->requestClient->request('DELETE', $this->base_url . '/cafe/client', [
					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => array_merge($this->request_body, ['client_id' => $client_id])
				]);

				$data = json_decode($response->getBody()->getContents(), true);

				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->session->set_flashdata('success', $data['message'] ?? '');
					$this->session->set_flashdata('data', $data['data']['client'] ?? '');
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
				//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
			} catch (RequestException $ex) {
				log_message('error', 'Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}
			redirect(site_url('dashboard/clients'));
		} else 
		if ($action == "edit") {
			//set form validation rules
			$this->form_validation->set_rules('email', $this->lang->line('email'), 'required|min_length[4]|valid_email', [
				'valid_email' => $this->lang->line('invalid_email'),
				'required' => $this->lang->line('email_required'),
				'min_length' => $this->lang->line('email_length')
			]);

			//update user
			if ($this->form_validation->run() == true) {

				try {
					$client['first_name'] = $this->input->post('first_name');
					$client['last_name'] = $this->input->post('last_name');
					$client['client_email'] = $this->input->post('email');
					$client['phone_number'] = $this->input->post('phone_number');
					$client['occupation'] = $this->input->post('occupation');
					$client['remark'] = $this->input->post('remark');
					$client['client_id'] = $client_id;

					$response = $this->requestClient->request('POST', $this->base_url . '/cafe/client', [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers,
						'json' => array_merge($this->request_body, $client),
					]);

					$data = json_decode($response->getBody()->getContents(), true);

					if ($response->getStatusCode() == 200) {
						$this->setData([
							'user' => $data['data']['user'][0] ?? []
						]);
						$this->session->set_flashdata('success', $data['message'] ?? '');
					} else {
						$this->session->set_flashdata('error', $data['message'] ?? '');
					}
					log_message('error', 'update error. ' . implode('-', $client));
				} catch (RequestException $ex) {
					log_message('error', 'Error updating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} catch (Exception $ex) {
					log_message('error', 'Generic Error updating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				}
			} else {
				$message = stripcslashes(stripslashes(html_entity_decode(strip_tags(validation_errors()))));
				$this->session->set_flashdata('error', $this->lang->line('failure') . ' . ' . $message);
				//$this->setBody('admin/user')->loadView();
			}
			redirect(site_url('dashboard/client/view/' . $client_id), 'location');
			//redirect(site_url('dashboard'));
		} else {

			try {
				$response = $this->requestClient->request('GET', $this->base_url . '/client?' . http_build_query(array_merge($this->request_body, ['client_id' => $client_id])), [


					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => ['client_id' => $client_id]
				]);

				$data = json_decode($response->getBody()->getContents(), true);
				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->setData([
						'client' => $data['data']['client'][0] ?? []
					]);
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
			} catch (RequestException $ex) {
				log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error creating franchise. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}
			$this->setBody('admin/client')->loadView();
		}
	}


	/**
	 * list clients of the system
	 *
	 * @return void
	 */
	public function clients()
	{
		$this->init_rbac();
		$this->session->set_flashdata('client_menu', 'active');
		//redirect user to home page if not admin
		$request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);
		try {
			$response = $this->httpAdapter->sendRequest($request);
			$clients = json_decode($response->getBody()->getContents(), true);

			$this->setData([
				'clients' => $clients['data']['clients']
			]);


			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching clients. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/clients')->loadView();
	}

	public function client_add()
	{
		$client['first_name'] = $this->input->post('first_name');
		$client['last_name'] = $this->input->post('last_name');
		$client['client_email'] = $this->input->post('email');
		$client['occupation'] = $this->input->post('occupation');
		$client['phone_number'] = $this->input->post('phone_number');
		$client['remark'] = $this->input->post('remark');
		$client['client_category'] = $this->input->post('category');
		$client['user_id'] = $this->session->user->user_id;
		//log_message('error', 'user data. ' . implode('=', $franchise));
		//save user
		try {
			//$response = $this->httpAdapter->sendRequest($request);
			$response = $this->requestClient->request('PUT', '/cafe-referral/cafe/client', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ($client)
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($response->getStatusCode() == 201) {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}

		redirect(site_url('dashboard/clients'));
	}

	/**
	 * List/view all commissions
	 *
	 * @return void
	 */
	public function commissions()
	{
		$this->init_rbac();
		$this->session->set_flashdata('commission_menu', 'active');

		$request = new Request('GET', $this->base_url . '/commissions?' . http_build_query($this->request_body), $this->headers);
		$client_request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);
		try{
			$response = $this->httpAdapter->sendRequest($request);
			$client_response = $this->httpAdapter->sendRequest($client_request);

			$commissions = json_decode($response->getBody()->getContents(), true);
			$clients = json_decode($client_response->getBody()->getContents(), true);

			$this->setData([
				'commissions' => $commissions['data']['commissions'],
				'clients' => $clients['data']['clients']
			]);
		}catch(RequestException $ex){
			log_message('error', 'Error fetching commissions. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/commissions')->loadView();

	}

	/**
	 * List/view all campaigns
	 *
	 * @return void
	 */
	public function campaigns()
	{
		$this->init_rbac();
		$this->session->set_flashdata('campaign_menu', 'active');
		//redirect user to home page if not admin
		$request = new Request('GET', $this->base_url . '/campaigns?' . http_build_query($this->request_body), $this->headers);
		$client_request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);
		try{
			$response = $this->httpAdapter->sendRequest($request);
			$client_response = $this->httpAdapter->sendRequest($client_request);

			$campaigns = json_decode($response->getBody()->getContents(), true);
			$clients = json_decode($client_response->getBody()->getContents(), true);

			$this->setData([
				'campaigns' => $campaigns['data']['campaigns'],
				'clients' => $clients['data']['clients']
			]);
		}catch(RequestException $ex){
			log_message('error', 'Error fetching campaigns. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/campaigns')->loadView();
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function rdv($action, $rdv_id)
	{
		try {
			$response = $this->requestClient->request('GET', $this->base_url . '/rdv?' . http_build_query(array_merge($this->request_body, ['rdv_id' => $rdv_id])), [

				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ['rdv_id' => $rdv_id]
			]);

			$data = json_decode($response->getBody()->getContents(), true);
			$clients_client_id = $data['data']['rdv'][0]['clients_client_id'];

			// get user information 
			$client_response = $this->requestClient->request('GET', $this->base_url . '/client?' . http_build_query(array_merge($this->request_body, ['client_id' => $clients_client_id])), [


				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ['client_id' => $clients_client_id]
			]);
			$client_info = json_decode($client_response->getBody()->getContents(), true);
			// var_dump($client_info['data']['client'][0]);
			// die;
			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->setData([
					'rdv' => $data['data']['rdv'][0] ?? [],
					'client' => $client_info['data']['client'][0] ?? []
				]);
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating franchise. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}
		$this->setBody('admin/rdv')->loadView();
	}


	public function campaign_add()
	{
		$period = explode(' - ', $this->input->post('campaign_period'));
		$campaign['campaign_client_id'] = implode(",", $this->input->post('campaign_client') ?? []);
		$campaign['start_time'] = explode(' ', $period[0])[1] ?? '';
		$campaign['start_date'] = explode(' ', $period[0])[0] ?? '';
		$campaign['end_time'] = explode(' ', $period[1])[1] ?? '';
		$campaign['end_date'] = explode(' ', $period[1])[0] ?? '';
		$campaign['contacts'] = count($this->input->post('campaign_client') ?? []);
		$campaign['config'] = $this->input->post('campaign_tax');
		$campaign['user_id'] = $this->session->user->user_id;
		//die(var_dump($campaign));
		//save campaign
		try {
			$response = $this->requestClient->request('PUT', $this->base_url . '/campaign', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ($campaign)
			]);

			$data = json_decode($response->getBody()->getContents(), true);
			if ($response->getStatusCode() == 201) {
				$this->session->set_flashdata('success', $data['message'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error creating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}

		redirect(site_url('dashboard/campaigns'));
	}

	public function campaign($action, $id)
	{
		$this->session->set_flashdata('campaign_menu', 'active');

		if ($action == 'delete') { //delete a campaign
			try {
				$response = $this->requestClient->request('DELETE', $this->base_url . '/campaign', [
					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => [
						'user_id' => $this->session->user->user_id,
						'campaign_id' => $id,
						'user_role' => $this->session->user->role_id
					]
				]);

				$data = json_decode($response->getBody()->getContents(), true);

				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->session->set_flashdata('success', $data['message'] ?? '');
					$this->session->set_flashdata('data', $data['data']['campaign'] ?? '');
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
			} catch (RequestException $ex) {
				log_message('error', 'Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}

			redirect(site_url('dashboard/campaigns'));
		} else {
			if ($action == "edit") { //edit campaign
				//set form validation rules
				$this->form_validation->set_rules('campaign_period_edit', $this->lang->line('period'), 'required');
				$this->form_validation->set_rules('campaign_client_edit', $this->lang->line('client'), 'required');

				if ($this->form_validation->run() == true) {
					try {
						$period = explode(' - ', $this->input->post('campaign_period_edit'));
						$campaign['campaign_client_id'] = intval($this->input->post('campaign_client_edit'));
						$campaign['start_time'] = explode(' ', $period[0])[1] ?? '';
						$campaign['start_date'] = explode(' ', $period[0])[0] ?? '';
						$campaign['end_time'] = explode(' ', $period[1])[1] ?? '';
						$campaign['end_date'] = explode(' ', $period[1])[0] ?? '';
						$campaign['contacts'] = $this->input->post('campaign_contacts_edit');
						$campaign['campaign_id'] = $id;

						$response = $this->requestClient->request('POST', $this->base_url . '/campaign', [
							'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
							'headers' => $this->headers,
							'json' => array_merge($this->request_body, $campaign)
						]);

						$data = json_decode($response->getBody()->getContents(), true);

						if ($response->getStatusCode() == 200) {
							$this->setData([
								'campaign' => $data['data']['campaign'][0] ?? []
							]);
							$this->session->set_flashdata('success', $data['message'] ?? '');
						} else {
							$this->session->set_flashdata('error', $data['message'] ?? '');
						}
						log_message('error', 'update error. ' . implode('-', $campaign));
					} catch (RequestException $ex) {
						log_message('error', 'Error updating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					} catch (Exception $ex) {
						log_message('error', 'Generic Error updating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					}
				} else {
					$message = stripcslashes(stripslashes(html_entity_decode(strip_tags(validation_errors()))));
					$this->session->set_flashdata('error', $this->lang->line('failure') . ' . ' . $message);
				}
				redirect(site_url('dashboard/campaign/view/' . $id), 'location');
			} else if ($action == 'receipt') { //generate campaign receipt
				try {
					$response = $this->requestClient->request('GET', $this->base_url . '/campaign?' . http_build_query(['campaign' => $id]), [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers,
						'form_params' => ['campaign_id' => $id]
					]);

					$data = json_decode($response->getBody()->getContents(), true);

					if ($data['status'] == 'success') {
						//@todo Send email to the user about created account
						$this->setData([
							'campaign' => $data['data']['campaign'][0] ?? []
						]);
					} else {
						$this->session->set_flashdata('error', $data['message'] ?? '');
					}
					//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
				} catch (RequestException $ex) {
					log_message('error', 'Error fetching campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} catch (Exception $ex) {
					log_message('error', 'Generic Error campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				}

				$this->setBody('admin/campaign')->loadView();
			} else if ($action == 'view') {
				//view campaign
				try {
					$response = $this->requestClient->request('GET', $this->base_url . '/campaign?' . http_build_query([
						'campaign_id' => $id,
						'user_id' => $this->session->user->user_id,
						'user_role' => $this->session->user->role_id
					]), [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers
					]);

					$data = json_decode($response->getBody()->getContents(), true);

					if ($data['status'] == 'success') {
						//@todo Send email to the user about created account
						$this->setData([
							'campaign' => $data['data']['campaign'][0] ?? [],
							'clients' => $data['data']['clients'] ?? []
						]);
					} else {
						$this->session->set_flashdata('error', $data['message'] ?? '');
					}
					//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
				} catch (RequestException $ex) {
					log_message('error', 'Error fetching campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} catch (Exception $ex) {
					log_message('error', 'Generic Error campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				}

				$this->setBody('admin/campaign')->loadView();
			} else if ($action == 'end') { //end campaign
				try {
					$campaign['campaign_id'] = $id;

					$response = $this->requestClient->request('PATCH', $this->base_url . '/campaign', [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers,
						'json' => array_merge($this->request_body, $campaign)
					]);

					$data = json_decode($response->getBody()->getContents(), true);

					if ($response->getStatusCode() == 200) {
						$this->setData([
							'campaign' => $data['data']['campaign'][0] ?? []
						]);
						$this->session->set_flashdata('success', $data['message'] ?? '');
					} else {
						$this->session->set_flashdata('error', $data['message'] ?? '');
					}
				} catch (RequestException $ex) {
					log_message('error', 'Error updating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} catch (Exception $ex) {
					log_message('error', 'Generic Error updating campaign. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
					$this->session->set_flashdata('error', $ex->getMessage());
				} finally {
					redirect(site_url('dashboard/campaigns'), 'location');
				}
			}
		}
	}

	private function exportAsPdf($data = null, $name =  null, $type = 'facture_pdf')
	{
		if ($data == null)
			return null;
		else {
			$htmlData = $this->load->view('pdfs/' . $type, $data, TRUE);
			//echo $htmlData;die();
			if ($name != null)
				$this->pdf->getPdfAsFile($htmlData, $name);
			else
				return $this->pdf->getPdf($htmlData);
		}
	}

	public function rdvs(){
		$this->init_rbac();
		$this->session->set_flashdata('rdv_menu', 'active');
		$rdv_request = new Request('GET', $this->base_url . '/rdvs?' . http_build_query($this->request_body), $this->headers);
		$client_request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);

		try {
			$rdv_response = $this->httpAdapter->sendRequest($rdv_request);
			$rdvs = json_decode($rdv_response->getBody()->getContents(), true);
			$client_response = $this->httpAdapter->sendRequest($client_request);
			$clients = json_decode($client_response->getBody()->getContents(), true);

			$this->setData([
				'clients' => $clients['data']['clients'],
				'rdvs' => $rdvs['data']['rdvs']
			]);

		} catch (RequestException $ex) {
			log_message('error', 'Error fetching rdvs. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}

		$this->setBody('admin/rdvs')->loadView();
	}

	/**
	 * add a new rvd 
	 *
	 * @return void
	 */
	public function rdv_add()
	{
		$rdv['rdv_date'] = $this->input->post('rdv_date');
		$rdv['rdv_time'] = $this->input->post('rdv_time');
		$rdv['rdv_client_id'] = $this->input->post('rdv_client_id');
		// print_r(array_merge($client, $this->request_body,));

		try {
			$response = $this->requestClient->request('PUT', $this->base_url . '/rdv', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => (array_merge($this->request_body, $rdv))
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
				$this->session->set_flashdata('data', $data['data']['user'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
		} catch (RequestException $ex) {
			log_message('error', 'Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}
		redirect(site_url('dashboard/rdvs'));
	}
	/**
	 * update an given rdv 
	 *
	 * @return void
	 */
	public function rdv_edit()
	{
	}

	/**
	 * List all  messages
	 *
	 * @return void
	 */
	public function messages()
	{

		$this->init_rbac();
		$this->session->set_flashdata('message_menu', 'active');

		$request = new Request('GET', $this->base_url . '/messages?' . http_build_query($this->request_body), $this->headers);
		$client_request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);

		try {
			$response = $this->httpAdapter->sendRequest($request);
			$messages_data = json_decode($response->getBody()->getContents(), true);
			$client_response = $this->httpAdapter->sendRequest($client_request);
			$clients = json_decode($client_response->getBody()->getContents(), true);


			$this->setData([
				'clients' => $clients['data']['clients'],
				'messages' => $messages_data['data']['taxes']
			]);
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching documents. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/messages')->loadView();
	}

	/**
	 * Send new message an save to database
	 */
	public function message_add()
	{
		$message_recipients = $this->input->post('message_recipients');
		$message['message_subject'] = $this->input->post('message_subject');
		$message['message_content'] = $this->input->post('message_content');

		foreach ($message_recipients as $message_recipient) {
			$message['client_id'] = $message_recipient;
			//Todo send message and send message status
			$message['status'] = "PENDING";
			try {
				$response = $this->requestClient->request('PUT', $this->base_url . '/message', [
					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => (array_merge($this->request_body, $message))
				]);
				// var_dump($response);
				$data = json_decode($response->getBody()->getContents(), true);

				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->session->set_flashdata('success', $data['message'] ?? '');
					$this->session->set_flashdata('data', $data['data']['user'] ?? '');
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
			} catch (RequestException $ex) {
				log_message('error', 'Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}
		}
		redirect(site_url('dashboard/messages'));
	}
	/**
	 * delete given message from database
	 *
	 * @return void
	 */
	public function message_delete($message_id)
	{
		try {
			$response = $this->requestClient->request('DELETE', $this->base_url . '/message', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => array_merge($this->request_body, ['message_id' => $message_id])
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
				$this->session->set_flashdata('data', $data['data']['client'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}
		redirect(site_url('dashboard/documents'));
	}

	/**
	 * show message
	 *
	 * @return void
	 */
	public function message($message_id)
	{
		$this->init_rbac();
		$this->session->set_flashdata('message_menu', 'active');
		try {

			$response = $this->requestClient->request('GET', $this->base_url . '/message?' . http_build_query(array_merge($this->request_body, ['message_id' => $message_id])), [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'json' => array_merge($this->request_body, ['$message_id' => $message_id])
			]);

			$data = json_decode($response->getBody()->getContents(), true);
			// var_dump($data['data']['message'][0]);
			// die;
			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account

				$this->setData([
					'message' => $data['data']['message'][0] ?? []
				]);
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
		} catch (RequestException $ex) {

			log_message('error', 'Error fetching stats. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {

			log_message('error', 'Generic Error creating franchise. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}
		$this->setBody('admin/message')->loadView();
	}

	/**
	 * update givent message
	 *
	 * @return void
	 */
	public function message_edit()
	{
	}

	/**
	 * Add new Document
	 *
	 * @return void
	 */
	public function document_add()
	{

		$document['document_type'] = $this->input->post('document_type');

		$config['upload_path'] = '.';
		$config['allowed_types'] = 'txt|pdf';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('document_name')) {
			// case - failure
			$upload_error = array('error' => $this->upload->display_errors());
			var_dump(is_writable('.'));
			die;
		} else {
			// case - success
			$upload_data = $this->upload->data();
			$document['document_name'] = $upload_data['file_name'];
		}
		// var_dump($document);
		// die;
		try {
			$response = $this->requestClient->request('PUT', $this->base_url . '/document', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => (array_merge($this->request_body, $document))
			]);

			$data = json_decode($response->getBody()->getContents(), true);

			if ($data['status'] == 'success') {
				//@todo Send email to the user about created account
				$this->session->set_flashdata('success', $data['message'] ?? '');
				$this->session->set_flashdata('data', $data['data']['user'] ?? '');
			} else {
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
		} catch (RequestException $ex) {
			log_message('error', 'Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		} catch (Exception $ex) {
			log_message('error', 'Generic Error creating user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}
		redirect(site_url('dashboard/documents'));
	}

	/**
	 * List all  documents
	 *
	 * @return void
	 */
	public function documents()
	{
		$this->init_rbac();
		$this->session->set_flashdata('document_menu', 'active');
		$request = new Request('GET', $this->base_url . '/documents?' . http_build_query($this->request_body), $this->headers);
		try{
			$response = $this->httpAdapter->sendRequest($request);
			$documents_data = json_decode($response->getBody()->getContents(), true);
			$this->setData([
				'documents' => $documents_data['data']['documents']
			]);
			// $data = $this->getData();
			// print_r($data['documents']);
			// die;
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		} catch (RequestException $ex) {
			log_message('error', 'Error fetching documents. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/documents')->loadView();
	}

	/**
	 * Act on a given document 
	 *
	 * @param [type] $action
	 * @param [type] $document_id
	 * @return void
	 */
	public function document($action, $document_id)
	{
		if ($action == 'delete') {
			try {
				$response = $this->requestClient->request('DELETE', $this->base_url . '/document', [
					'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
					'headers' => $this->headers,
					'form_params' => array_merge($this->request_body, ['document_id' => $document_id])
				]);

				$data = json_decode($response->getBody()->getContents(), true);

				if ($data['status'] == 'success') {
					//@todo Send email to the user about created account
					$this->session->set_flashdata('success', $data['message'] ?? '');
					$this->session->set_flashdata('data', $data['data']['client'] ?? '');
				} else {
					$this->session->set_flashdata('error', $data['message'] ?? '');
				}
				//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
			} catch (RequestException $ex) {
				log_message('error', 'Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			} catch (Exception $ex) {
				log_message('error', 'Generic Error deleting user. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				$this->session->set_flashdata('error', $ex->getMessage());
			}
			redirect(site_url('dashboard/documents'));
		} else {
		}
	}


	/**
	 * Validation method for password update to check if password is either empty, or at least 8 characters long
	 *
	 * @return boolean
	 */
	public function password_update_check($password)
	{
		if (strlen($password) == 0 || strlen($password) > 7) {
			return true;
		} else {
			$this->form_validation->set_message('password_update_check', $this->lang->line('invalid_password'));
			return false;
		}
	}

	/**
	 * Configure app settings (role, tax payments)
	 *
	 * @return void
	 */
	public function settings()
	{
		$this->init_rbac();
		$this->session->set_flashdata('admin_menu', 'active');
		$tax_request = new Request('GET', $this->base_url . '/taxes?' . http_build_query($this->request_body), $this->headers);
		$client_request = new Request('GET', $this->base_url . '/clients?' . http_build_query($this->request_body), $this->headers);
		$settings_request = new Request('GET', $this->base_url . '/settings?' . http_build_query($this->request_body), $this->headers);
		$roles_request = new Request('GET', $this->base_url . '/roles?' . http_build_query($this->request_body), $this->headers);
		try{
			$tax_response = $this->httpAdapter->sendRequest($tax_request);
			$client_response = $this->httpAdapter->sendRequest($client_request);
			$settings_response = $this->httpAdapter->sendRequest($settings_request);
			$roles_response = $this->httpAdapter->sendRequest($roles_request);

			$tax = json_decode($tax_response->getBody()->getContents(), true);
			$clients = json_decode($client_response->getBody()->getContents(), true);
			$settings = json_decode($settings_response->getBody()->getContents(), true);
			$roles = json_decode($roles_response->getBody()->getContents(), true);

			$this->setData([
				'taxes' => $tax['data']['taxes'],
				'clients' => $clients['data']['clients'],
				'settings' => $settings['data']['settings'],
				'permissions' => $settings['data']['permissions'],
				'roles' => $roles['data']['roles']
			]);
		}catch(RequestException $ex){
			log_message('error', 'Error fetching settings. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
		}
		$this->setBody('admin/settings')->loadView();
	}
	/**
	 * Configure app settings (commissions)
	 *
	 * @return void
	 */
	public function settings_add(){
		$this->init_rbac();
		$settings['config_commission_type'] = ($this->input->post('comm_type'));
		$settings['config_commission_amount'] = ($this->input->post('comm_type_amount'));
		$settings['config_telefonist_amount'] = ($this->input->post('comm_telefonist_amount'));
		$settings['config_tax_id'] = ($this->input->post('comm_tax'));
		$settings['config_currency'] = $this->input->post('comm_currency');
		$settings['user_id'] = $this->session->user->user_id;
		//save campaign
		try{
			$response = $this->requestClient->request('PUT', $this->base_url . '/settings', [
				'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
				'headers' => $this->headers,
				'form_params' => ($settings)
			]);

			$data = json_decode($response->getBody()->getContents(), true);
			if($response->getStatusCode() == 201){
				$this->session->set_flashdata('success', $data['message'] ?? '');
			}else{
				$this->session->set_flashdata('error', $data['message'] ?? '');
			}
			//log_message('error', 'stats error. '. implode('-',$franchises['data']['users']));
		}catch(RequestException $ex){
			log_message('error', 'Error creating settings. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}catch(Exception $ex){
			log_message('error', 'Generic Error creating settings. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
			$this->session->set_flashdata('error', $ex->getMessage());
		}

		redirect(site_url('dashboard/settings'));
	}

	public function tax($action, $id=0){
		$this->init_rbac();
		switch($action){
			case "add":
				try{
					$tax_data['tax_name'] = $this->input->post('tax_name');
					$tax_data['tax_rate'] = $this->input->post('tax_rate');
					$tax_data['tax_user_id'] = $this->session->user->user_id;
					$tax_response = $this->requestClient->request('PUT', $this->base_url . '/tax', [
						'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
						'headers' => $this->headers,
						'form_params' => ($tax_data)
					]);
					//die(var_dump($tax_data));
					$tax = json_decode($tax_response->getBody()->getContents(), true);
					if($tax_response->getStatusCode() == 201){
						$this->session->set_flashdata('success', $tax['message'] ?? '');
					}else{
						$this->session->set_flashdata('error', $tax['message'] ?? '');
					}
				}catch(RequestException $ex){
					log_message('error', 'Error saving tax. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				}finally{
					redirect('dashboard/settings');
				}
			break;

			case "edit":
				//set form validation rules
				$this->form_validation->set_rules('tax_name', $this->lang->line('tax_name'), 'required');
				$this->form_validation->set_rules('tax_rate', $this->lang->line('tax_rate'), 'required');

				if($this->form_validation->run() == true){
					try{
						$tax['tax_name'] = $this->input->post('tax_name');
						$tax['tax_rate'] = $this->input->post('tax_rate');
						$tax['tax_id'] = $this->input->post('tax_id');

						$response = $this->requestClient->request('POST', $this->base_url . '/tax', [
							'auth' => [$this->config->item('api_user'), $this->config->item('api_password')],
							'headers' => $this->headers,
							'json' => array_merge($this->request_body, $tax)
						]);

						$data = json_decode($response->getBody()->getContents(), true);

						if($response->getStatusCode() == 200){
							$this->session->set_flashdata('success', $data['message'] ?? '');
						}else{
							$this->session->set_flashdata('error', $data['message'] ?? '');
						}
						log_message('error', 'update error. '. implode('-',$tax));
					}catch(RequestException $ex){
						log_message('error', 'Error updating tax. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					}catch(Exception $ex){
						log_message('error', 'Generic Error updating tax. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
						$this->session->set_flashdata('error', $ex->getMessage());
					}
				}
				else{
					$message = stripcslashes(stripslashes(html_entity_decode(strip_tags(validation_errors()))));
					$this->session->set_flashdata('error', $this->lang->line('failure') . ' . ' . $message);
				}
				redirect(site_url('dashboard/settings'), 'location');
			break;

			case "view":
				$request = new Request('GET', $this->base_url . '/tax?' . http_build_query(array_merge(['tax_id'=>$id], $this->request_body)), $this->headers);
				try{
					$response = $this->httpAdapter->sendRequest($request);
					$tax = json_decode($response->getBody()->getContents(), true);
		
					$this->setData([
						'tax' => $tax['data']['tax'][0] ?? []
					]);
				}catch(RequestException $ex){
					log_message('error', 'Error fetching tax. ' . $ex->getMessage() . '. Trace : ' . $ex->getTraceAsString());
				}
				$this->setBody('admin/tax')->loadView();
			break;
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->session->set_flashdata('success', $this->lang->line('logout_success'));
		redirect(site_url('login'), 'location');
	}

	public function agenda()
	{
		$this->init_rbac();
		$this->session->set_flashdata('agenda_menu', 'active');
		$this->setBody('admin/agenda')->loadView();
	}
}
