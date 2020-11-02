<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @author [zeufack]
 * @email [zeufackp@gmail.com]
 * @create date 2020-10-02 14:47:11
 * @modify date 2020-10-02 14:47:11
 * @contributors []
 */

class Events extends CI_Model
{
	protected $table = 'events';
	protected $key = "event_id";
	protected $readable_fields = 'event_id, start_time, start_date, end_time, end_date, event.users_user_id, event.create_time, event.update_time';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function insert($data)
	{
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function get()
	{
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function getAll()
	{
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function update()
	{
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function delete()
	{
	}
}
