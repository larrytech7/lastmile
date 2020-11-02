<?php

/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * @date 15/08/2020
 * Contributors : 
 */
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

/**
 * Main API class for the Cafe referral/reseller project
 */
class Cafe extends RestController
{

	protected $user_role;
	protected $user_id;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('role');
		$this->load->model('client');
		$this->load->model('campaign');
		$this->load->model('commission');
		$this->load->model('message');
		$this->load->model('rdv');
		$this->load->model('document');
		$this->load->model('tax');
		$this->load->model('setting');

		$this->user_role = $this->_args['user_role'] ?? '';
		$this->user_id = $this->_args['user_id'] ?? '';
		//log_message('error', 'Global role/id from request : ' . $this->user_role . ' / '. $this->user_id);
	}

	/**
	 * Login/Authenticate user
	 * @todo Complete the authentication
	 * @return array REST response
	 * @deprecated 0.1 not used
	 */
	public function login_post()
	{
		$login['email'] = $this->post('email');
		$login['password'] = $this->post('password');
	}

	/**
	 * Return global summaries about entities
	 *
	 * @return array REST response
	 */
	public function stats_get()
	{
		if ($this->user_role == 1) //ADMIN
			$stats = $this->user->stats();
		else
			$stats = $this->user->stats(['users_user_id' => $this->user_id]);

		$this->response(
			[
				'status' => $stats != null ? 'success' : 'error',
				'message' => sprintf($stats != null ? $this->lang->line('found') : $this->lang->line('not_found'), 'stats'),
				'data' => $stats
			],
			$stats != null ? RestController::HTTP_OK : RestController::HTTP_BAD_REQUEST
		);
	}

	/**
	 * Add new user record
	 *
	 * @return array REST response
	 */
	public function user_put()
	{
		$user['username'] = $this->put('username');
		$user['email'] = $this->put('email');
		$user['password'] = password_hash($this->put('password'), PASSWORD_BCRYPT);
		$user['name'] = $this->put('name');
		$user['user_status'] = $this->put('status');
		$user['user_phone_number'] = $this->put('user_phone_number');
		$user['user_address'] = $this->put('user_address');
		$user['roles_role_id'] = $this->put('role');

		//@todo check for user profile upload file
		$added = $this->user->insert($user);
		unset($user['password']);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('user_created') : $this->lang->line('user_not_created'), $user['username']),
				'data' => [$user]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_BAD_REQUEST
		);
	}

	/**
	 * Update user
	 *
	 * @return request status
	 */
	public function user_post()
	{

		$id = $this->post('user_id');
		$user['username'] = $this->post('username');
		$user['email'] = $this->post('email');
		//update password only if present
		$password = $this->post('password');
		if (strlen($password) > 7) {
			$user['password'] = password_hash($password, PASSWORD_BCRYPT);
		}
		$user['name'] = $this->post('name');
		$user['user_status'] = $this->post('status');
		$user['user_phone_number'] = $this->post('user_phone_number');
		$user['user_address'] = $this->post('user_address');

		$updated = $this->user->update($id, $user);
		unset($user['password']);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('user_updated') : $this->lang->line('user_not_updated'), $user['username']),
			'data' => [
				'user' => $user
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_MODIFIED);
	}

	/**
	 * Delete user
	 *
	 * @return request status
	 */
	public function user_delete()
	{

		$id = $this->delete('user_id', true);

		$deleted = $this->user->delete($id);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('user_deleted') : $this->lang->line('user_not_deleted'), ''),
			'data' => [
				'user' => []
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_MODIFIED);
	}


	/**
	 * Return a user
	 *
	 * @return mixed user info
	 */
	public function user_get()
	{
		$user_id = $this->get('user');
		$user = $this->user->get($user_id)->result_object();
		//log_message('error', 'get user. id : '.$user_id);

		$this->response([
			'status' => count($user) > 0 ? 'success' : 'error',
			'message' => count($user) > 0 ? $this->lang->line('user_read') : $this->lang->line('user_not_found'),
			'data' => [
				'user' => $user
			]
		], count($user) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of users
	 *
	 * @return mixed users info
	 */
	public function users_get()
	{
		$user_role = $this->user_role;
		if ($user_role == 1)
			$users = $this->user->getAll()->result_object();
		else $users = [];

		$this->response([
			'status' => count($users) > 0 ? 'success' : 'error',
			'message' => sprintf(count($users) > 0 ? $this->lang->line('user_read') : $this->lang->line('user_not_found'), ''),
			'data' => [
				'total' => count($users),
				'users' => $users
			]
		], count($users) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new role record
	 *
	 * @return array REST response
	 */
	public function role_put(){
		$role['role_name'] = $this->put('role_name');
		$role['permissions'] = $this->put('permissions');

		$added = $this->role->insert($role);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('role_created') : $this->lang->line('role_not_created'), $role['role_name']),
				'data' => ['role' => $role]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update role
	 *
	 * @return request status
	 */
	public function role_post()
	{

		$id = $this->post('role_id');
		$role['role_name'] = $this->post('role_name');
		$role['permissions'] = $this->post('permissions');
		//log_message('error', 'role ' . $id);

		$updated = $this->role->update($id, $role);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('role_updated') : $this->lang->line('role_not_updated'), $role['role_name']),
			'data' => [
				'role' => $role
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete role
	 *
	 * @return request status
	 */
	public function role_delete()
	{

		$id = $this->delete('role_id', true);
		$role = $this->delete('role');

		$deleted = $this->role->delete($id);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('role_deleted') : $this->lang->line('role_not_deleted'), $role),
			'data' => [
				'role' => $role
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a role
	 *
	 * @return mixed role info
	 */
	public function role_get()
	{
		$role_id = $this->get('role');
		$role_result = $this->role->get($role_id)->result_object();
		$role = [
			"role_id" => $role_result[0]->role_id,
			"role_name" => $role_result[0]->role_name,
			"permissions" => []
		];
		foreach($role_result as $r){
			array_push($role['permissions'], $r->permission_name);
		}
		   
		$this->response([
			'status' => count($role_result) > 0 ? 'success' : 'error',
			'message' => count($role_result) > 0 ? $this->lang->line('role_found') : $this->lang->line('role_not_found'),
			'data' => [
				'role' => $role
			]
		], count($role_result) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of roles
	 *
	 * @return mixed users info
	 */
	public function roles_get(){
		
		$roles_results = $this->role->getAll()->result_object();
		$roles = [];

		foreach($roles_results as $my_role){
			if(!array_key_exists($my_role->role_id, $roles)){
				//add role to list and push permission
				$roles[$my_role->role_id] = [];
			}
			array_push($roles[$my_role->role_id], ["permission_name" => $my_role->permission_name, "role_name" => $my_role->role_name]);
		}
		   
		$this->response([
			'status' => count($roles) > 0 ? 'success' : 'error',
			'message' => sprintf(count($roles) > 0 ? $this->lang->line('role_read') : $this->lang->line('role_not_found'), ''),
			'data' => [
				'total' => count($roles),
				'roles' => $roles
			]
		], count($roles) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new rdv record
	 *
	 * @return array REST response
	 */
	public function rdv_put()
	{
		
		$rdv['rdv_date'] = $this->put('rdv_date');
		$rdv['rdv_time'] = $this->put('rdv_time');
		$rdv['users_user_id'] = $this->user_id;
		$rdv['clients_client_id'] = $this->put('rdv_client_id');

		

		$added = $this->rdv->insert($rdv);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "rendez-vous ",  $rdv['rdv_date'] . ' ' . $rdv['rdv_time']),
				'data' => ['rdv' => $rdv]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update rdv
	 *
	 * @return request status
	 */
	public function rdv_post()
	{
		$user_role = $this->post('user_role');
		$user_id = $this->post('user_id');
		$id = $this->post('rdv_id');
		$rdv['rdv_date'] = $this->post('rdv_date');
		$rdv['rdv_time'] = $this->post('rdv_time');
		$rdv['clients_client_id'] = $this->post('rdv_client_id');

		if ($user_role == 1) //ADMIN
			$updated = $this->rdv->update($id, $rdv);
		else
			$updated = $this->rdv->update($id, $rdv, ['users_user_id' => $user_id]);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'Rendez-vous'),
			'data' => [
				'rdv' => $rdv
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete rdv
	 *
	 * @return request status
	 */
	public function rdv_delete()
	{
		$id = $this->delete('rdv_id', true);

		if ($this->user_role == 1) //ADMIN
			$deleted = $this->rdv->delete($id);
		else
			$deleted = $this->rdv->delete($id, ['users_user_id' => $this->user_id]);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'Rendez-vous'),
			'data' => [
				'rdv' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a rdv
	 *
	 * @return mixed rdv info
	 */
	public function rdv_get()
	{
		$user_role = $this->get('user_role');
		$user_id = $this->get('user_id');
		$rdv_id = $this->get('rdv_id');

		if ($user_role == 1) //ADMIN
			$rdv = $this->rdv->get($rdv_id)->result_object();
		else
			$rdv = $this->rdv->get($rdv_id, ['users_user_id' => $user_id])->result_object();

		$this->response([
			'status' => count($rdv) > 0 ? 'success' : 'error',
			'message' => count($rdv) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'),
			'data' => [
				'rdv' => $rdv
			]
		], count($rdv) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of rdv
	 *
	 * @return mixed users info
	 */
	public function rdvs_get()
	{
		$user_role = $this->get('user_role');
		$user_id = $this->get('user_id');
		if ($user_role == 1)
			$rdvs = $this->rdv->getAll()->result_object();
		else $rdvs = $this->rdv->getWhere(['users_user_id' => $user_id])->result_object();

		$this->response([
			'status' => count($rdvs) > 0 ? 'success' : 'error',
			'message' => sprintf(count($rdvs) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'Rendez-vous'),
			'data' => [
				'total' => count($rdvs),
				'rdvs' => $rdvs
			]
		], count($rdvs) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new client record
	 *
	 * @return array REST response
	 */
	public function client_put()
	{
		$client['first_name'] = $this->put('first_name');
		$client['last_name'] = $this->put('last_name');
		$client['client_email'] = $this->put('client_email');
		$client['phone_number'] = $this->put('phone_number');
		$client['occupation'] = $this->put('occupation');
		$client['remark'] = $this->put('remark');
		$client['client_category'] = $this->put('client_category');
		$client['users_user_id'] = $this->user_id;

		$added = $this->client->insert($client);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "Client ",  $client['first_name'] . ' ' . $client['last_name']),
				'data' => ['client' => $client]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update client
	 *
	 * @return request status
	 */
	public function client_post()
	{
		$id = $this->post('client_id');
		$client['first_name'] = $this->post('first_name');
		$client['last_name'] = $this->post('last_name');
		$client['client_email'] = $this->post('client_email');
		$client['phone_number'] = $this->post('phone_number');
		$client['occupation'] = $this->post('occupation');
		$client['client_category'] = $this->post('client_category');
		$client['remark'] = $this->post('remark');


		if ($this->user_role == 1) { //ADMIN
			$updated = $this->client->update($id, $client);
		} else {
			$updated = $this->client->update($id, $client, ['users_user_id' => $this->user_id]);
			log_message('error', 'client ' . $updated);
		}

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'Client ' . $client['first_name'] . ' ' . $client['last_name']),
			'data' => [
				'client' => $client
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete client
	 *
	 * @return request status
	 */
	public function client_delete()
	{

		$id = $this->delete('client_id', true);

		if ($this->user_role == 1) //ADMIN
		{
			$deleted = $this->client->delete($id);
		} else {
			$deleted = $this->client->delete($id, ['users_user_id' => $this->user_id]);
		}
		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'Client'),
			'data' => [
				'client' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a client
	 *
	 * @return mixed client info
	 */
	public function client_get()
	{
		$client_id = $this->get('client_id');

		if ($this->user_role == 1) //ADMIN
			$client = $this->client->get($client_id)->result_object();
		else
			$client = $this->client->get($client_id, ['users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($client) > 0 ? 'success' : 'error',
			'message' => sprintf(count($client) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'client'),
			'data' => [
				'client' => $client
			]
		], count($client) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of clients
	 *
	 * @return mixed clients info
	 */
	public function clients_get()
	{
		if ($this->user_role == 1) //ADMIN
			$clients = $this->client->getAll()->result_object();
		else
			$clients = $this->client->getWhere(['users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($clients) > 0 ? 'success' : 'error',
			'message' => sprintf(count($clients) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'Client(s)'),
			'data' => [
				'total' => count($clients),
				'clients' => $clients
			]
		], count($clients) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new tax record
	 *
	 * @return array REST response
	 */
	public function tax_put(){
		$tax['tax_name'] = $this->put('tax_name');
		$tax['tax_rate'] = $this->put('tax_rate');
		$tax['tax_user_id'] = $this->put('tax_user_id');

		$added = $this->tax->insert($tax);

		$this->response([
			'status' => $added ? 'success' : 'error',
			'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "Tax ",  $tax['tax_name']),
			'data' => ['tax' => $tax]
		], 
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Update Tax
	 *
	 * @return request status
	 */
	public function tax_post()
	{
		$id = $this->post('tax_id');
		$tax['tax_name'] = $this->post('tax_name');
		$tax['tax_rate'] = $this->post('tax_rate');

		$updated = false;
		if ($this->user_role == 1) //ADMIN
			$updated = $this->tax->update($id, $tax);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'tax ' . $tax['tax_name']),
			'data' => [
				'tax' => $tax
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete tax
	 *
	 * @return request status
	 */
	public function tax_delete()
	{

		$id = $this->delete('tax_id', true);

		$deleted = false;
		if ($this->user_role == 1) //ADMIN
			$deleted = $this->tax->delete($id);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'Tax'),
			'data' => [
				'tax' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a tax
	 *
	 * @return mixed tax info
	 */
	public function tax_get()
	{
		$tax_id = $this->get('tax_id');

		$tax = $this->tax->get($tax_id)->result_object();

		$this->response([
			'status' => count($tax) > 0 ? 'success' : 'error',
			'message' => sprintf(count($tax) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'tax'),
			'data' => [
				'tax' => $tax
			]
		], count($tax) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of taxes
	 *
	 * @return mixed clients info
	 */
	public function taxes_get()
	{

		$taxes = $this->tax->getAll()->result_object();

		$this->response([
			'status' => count($taxes) > 0 ? 'success' : 'error',
			'message' => sprintf(count($taxes) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'Tax(es)'),
			'data' => [
				'total' => count($taxes),
				'taxes' => $taxes
			]
		], count($taxes) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new document record
	 *
	 * @return array REST response
	 */
	public function document_put()
	{
		$document['document_type'] = $this->put('document_type');
		$document['document_name'] = $this->put('document_name');
		$document['users_user_id'] = $this->user_id;

		$added = $this->document->insert($document);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "Document ",  $document['document_name']),
				'data' => ['document' => $document]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update document
	 *
	 * @return request status
	 */
	public function document_post()
	{
		$id = $this->post('document_id');
		$document['document_name'] = $this->post('document_name');
		$document['document_type'] = $this->post('document_type');

		$updated = $this->document->update($id, $document);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'document_name ' . $document['document_name']),
			'data' => [
				'document' => $document
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete document
	 *
	 * @return request status
	 */
	public function document_delete()
	{

		$id = $this->delete('document_id', true);

		if ($this->user_role == 1) //ADMIN
			$deleted = $this->document->delete($id);
		else
			$deleted = $this->document->delete($id, ['users_user_id' => $this->user_id]);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'document'),
			'data' => [
				'document' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a document
	 *
	 * @return mixed document info
	 */
	public function document_get()
	{
		$document_id = $this->get('document_id');

		if ($this->user_role == 1) //ADMIN
			$document = $this->document->get($document_id)->result_object();
		else
			$document = $this->document->get($document_id, ['users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($document) > 0 ? 'success' : 'error',
			'message' => sprintf(count($document) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'document'),
			'data' => [
				'tax' => $document
			]
		], count($document) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of documents
	 *
	 * @return mixed clients info
	 */
	public function documents_get()
	{

		if ($this->user_role == 1) //ADMIN
			$documents = $this->document->getAll()->result_object();
		else
			$documents = $this->document->getAll(['users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($documents) > 0 ? 'success' : 'error',
			'message' => sprintf(count($documents) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'document(s)'),
			'data' => [
				'total' => count($documents),
				'documents' => $documents
			]
		], count($documents) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new message record
	 *
	 * @return array REST response
	 */
	public function message_put()
	{
		$message['subject'] = $this->put('message_subject');
		$message['message'] = $this->put('message_content');
		$message['users_user_id'] = $this->user_id;
		$message['clients_client_id'] = $this->put('client_id');
		$message['status'] = $this->put('status');

		$added = $this->message->insert($message);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "Message ",  $message['subject']),
				'data' => ['message' => $message]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update message
	 *
	 * @return request status
	 */
	public function message_post()
	{
		$id = $this->post('message_id');
		$message['subject'] = $this->post('message_subject');
		$message['message'] = $this->post('message_content');
		$message['status'] = $this->post('status');

		if ($this->user_role == 1) //ADMIN
			$updated = $this->message->update($id, $message);
		else
			$updated = $this->message->update($id, $message, ['users_user_id' => $this->user_id]);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'message ' . $message['subject']),
			'data' => [
				'message' => $message
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete message
	 *
	 * @return request status
	 */
	public function message_delete()
	{

		$id = $this->delete('message_id', true);

		if ($this->user_role == 1) //ADMIN
			$deleted = $this->message->delete($id);
		else
			$deleted = $this->message->delete($id, ['messages.users_user_id' => $this->user_id]);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'message'),
			'data' => [
				'message' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a message
	 *
	 * @return mixed message info
	 */
	public function message_get()
	{
		$message_id = $this->get('message_id');

		if ($this->user_role == 1) //ADMIN
			$message = $this->message->get($message_id)->result_object();
		else
			$message = $this->message->get($message_id, ['messages.users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($message) > 0 ? 'success' : 'error',
			'message' => sprintf(count($message) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'message'),
			'data' => [
				'message' => $message
			]
		], count($message) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of messages
	 *
	 * @return mixed clients info
	 */
	public function messages_get()
	{

		if ($this->user_role == 1) //ADMIN
			$messages = $this->message->getAll()->result_object();
		else
			$messages = $this->message->getAll(['messages.users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($messages) > 0 ? 'success' : 'error',
			'message' => sprintf(count($messages) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'message(s)'),
			'data' => [
				'total' => count($messages),
				'taxes' => $messages
			]
		], count($messages) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Add new campaign record
	 *
	 * @return array REST response
	 */
	public function campaign_put()
	{
		$campaign['start_time'] = $this->put('start_time');
		$campaign['start_date'] = $this->put('start_date');
		$campaign['end_time'] = $this->put('end_time');
		$campaign['end_date'] = $this->put('end_date');
		$campaign['campaign_client_id'] = $this->put('campaign_client_id');
		$campaign['config'] = $this->put('config');
		$campaign['users_user_id'] = $this->user_id;
		$campaign['contacts'] = $this->put('contacts');

		$added = $this->campaign->insert($campaign);

		$this->response(
			[
				'status' => $added ? 'success' : 'error',
				'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "campaign ",  $campaign['start_time'] . ', ' . $campaign['start_date']),
				'data' => ['campaign' => $campaign]
			],
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE
		);
	}

	/**
	 * Update campaign
	 *
	 * @return request status
	 */
	public function campaign_post()
	{
		$id = $this->post('campaign_id');
		$campaign['start_time'] = $this->post('start_time');
		$campaign['start_date'] = $this->post('start_date');
		$campaign['end_time'] = $this->post('end_time');
		$campaign['end_date'] = $this->post('end_date');
		$campaign['contacts'] = $this->post('contacts');
		$campaign['campaign_client_id'] = $this->post('campaign_client_id');

		if ($this->user_role == 1) //ADMIN
			$updated = $this->campaign->update($id, $campaign);
		else
			$updated = $this->campaign->update($id, $campaign, ['campaigns.users_user_id' => $this->user_id]);

		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'campaign ' . $campaign['start_time'] . ', ' . $campaign['start_date']),
			'data' => [
				'campaign' => $campaign
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}
	
	
	/**
	 * End campaign
	 *
	 * @return void
	 */
	public function campaign_patch(){
		$id = $this->patch('campaign_id');
		$campaign['end_time'] = date('H:m:s');
		$campaign['end_date'] = date('Y-m-d');

		//check if the campaign is not already ended
		$current_campaign = $this->campaign->get($id)->result_object()[0];
		if($current_campaign->end_date == $campaign['end_date']){
			$updated = false;
		}else{
			if($this->user_role == 1 ) //ADMIN
				$updated = $this->campaign->update($id, $campaign);
			else
				$updated = $this->campaign->update($id, $campaign, ['campaigns.users_user_id' => $this->user_id]);
		}
		$this->response([
			'status' => $updated ? 'success' : 'error',
			'message' => sprintf($updated ? $this->lang->line('updated') : $this->lang->line('not_updated'), 'campaign ' . $campaign['end_time'] .', ' .$campaign['end_date']),
			'data' => [
				'campaign' => $campaign
			]
		], $updated ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Delete campaign
	 *
	 * @return request status
	 */
	public function campaign_delete()
	{

		$id = $this->delete('campaign_id', true);

		if ($this->user_role == 1) //ADMIN
			$deleted = $this->campaign->delete($id);
		else
			$deleted = $this->campaign->delete($id, ['campaigns.users_user_id' => $this->user_id]);

		$this->response([
			'status' => $deleted ? 'success' : 'error',
			'message' => sprintf($deleted ? $this->lang->line('deleted') : $this->lang->line('not_deleted'), 'campaign'),
			'data' => [
				'campaign' => $id
			]
		], $deleted ? RestController::HTTP_OK : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return a campaign
	 *
	 * @return mixed campaign info
	 */
	public function campaign_get()
	{
		$campaign_id = $this->get('campaign_id');

		if ($this->user_role == 1) //ADMIN
			$campaign = $this->campaign->get($campaign_id)->result_object();
		else
			$campaign = $this->campaign->get($campaign_id, ['campaigns.users_user_id' => $this->user_id])->result_object();
		if($this->user_role == 1 ) //ADMIN
			$clients = $this->client->getAll()->result_object();
		else 
			$clients = $this->client->getWhere(['users_user_id' => $this->user_id])->result_object();
		
		$this->response([
			'status' => count($campaign) > 0 ? 'success' : 'error',
			'message' => sprintf(count($campaign) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'campaign'),
			'data' => [
				'campaign' => $campaign,
				'clients' => $clients
			]
		], count($campaign) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Return list of campaigns
	 *
	 * @return mixed campaigns info
	 */
	public function campaigns_get()
	{

		if ($this->user_role == 1) //ADMIN
			$campaigns = $this->campaign->getAll()->result_object();
		else
			$campaigns = $this->campaign->getAll(['campaigns.users_user_id' => $this->user_id])->result_object();

		$this->response([
			'status' => count($campaigns) > 0 ? 'success' : 'error',
			'message' => sprintf(count($campaigns) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'campaign(s)'),
			'data' => [
				'total' => count($campaigns),
				'campaigns' => $campaigns
			]
		], count($campaigns) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}
	
	public function commissions_get(){

		if($this->user_role == 1 ) //ADMIN
			$commissions = $this->commission->getAll()->result_object();
		else
			$commissions = $this->commission->getAll(['commissions.users_user_id' => $this->user_id])->result_object();
		   
		$this->response([
			'status' => count($commissions) > 0 ? 'success' : 'error',
			'message' => sprintf(count($commissions) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'commission(s)'),
			'data' => [
				'total' => count($commissions),
				'commissions' => $commissions
			]
		], count($commissions) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	/**
	 * Save new settings
	 *
	 * @return void
	 */
	public function settings_put(){
		$setting['config_commission_type'] = $this->put('config_commission_type');
		$setting['config_commission_amount'] = $this->put('config_commission_amount');
		$setting['config_telefonist_amount'] = $this->put('config_telefonist_amount');
		$setting['config_tax_id'] = $this->put('config_tax_id');
		$setting['config_currency'] = $this->put('config_currency');
		$setting['config_user_id'] = $this->user_id;

		$added = $this->setting->insert($setting);

		$this->response([
			'status' => $added ? 'success' : 'error',
			'message' => sprintf($added ? $this->lang->line('created') : $this->lang->line('not_created'), "parametres ",  $setting['config_commission_type'] ),
			'data' => ['setting' => $setting]
		], 
			$added ? RestController::HTTP_CREATED : RestController::HTTP_NOT_ACCEPTABLE);
	}

	/**
	 * Return list of settings
	 *
	 * @return mixed settings info
	 */
	public function settings_get(){

		if($this->user_role == 1 ) //ADMIN
			$settings = $this->setting->getAll()->result_object();
		else //othe users
			$settings = $this->setting->getAll(['settings.config_user_id' => $this->user_id])->result_object();
		$permissions = $this->setting->getPermissions()->result_object();
		   
		$this->response([
			'status' => count($settings) > 0 ? 'success' : 'error',
			'message' => sprintf(count($settings) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'parametre(s)'),
			'data' => [
				'total' => count($settings),
				'settings' => $settings,
				'permissions' => $permissions
			]
		], count($settings) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	//get all system permissions
	public function permissions_get(){
		$permissions = $this->setting->getPermissions()->result_object();
		   
		$this->response([
			'status' => count($permissions) > 0 ? 'success' : 'error',
			'message' => sprintf(count($permissions) > 0 ? $this->lang->line('found') : $this->lang->line('not_found'), 'permission(s)'),
			'data' => [
				'total' => count($permissions),
				'permissions' => $permissions
			]
		], count($permissions) > 0 ? RestController::HTTP_OK : RestController::HTTP_NOT_FOUND);
	}

	private function getHeader($key = '')
	{
		return $this->_head_args[strtolower($key)];
	}

	private function guidv4()
	{
		if (function_exists('com_create_guid') === true)
			return trim(com_create_guid(), '{}');

		$data = openssl_random_pseudo_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
}
