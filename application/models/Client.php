<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Client extends CI_Model
{
    protected $table = 'clients';
    protected $key = "client_id";
    protected $readable_fields = 'client_id, first_name, last_name, phone_number, remark, client_category, client_email, occupation, users_user_id, clients.create_time, clients.update_time, username, name, email, profile_image '; 

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Insert new record
     *
     * @param array $data
     * @return boolean whether or not the insert was done
     */
    public function insert($data){
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Get single record
     *
     * @param int record id
     * @return array record
     */
    public function get($id, $where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = clients.users_user_id')
            ->where($this->key, $id)
            ->where($where)
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->get($this->table);
    }

    public function getWhere($where){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = clients.users_user_id')
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->where($where)
            ->get($this->table);
    }
    
    /**
     * Get all records. Only user with role admin can read all users
     *
     * @param string role
     * @return array record(s)
     */
    public function getAll(){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = clients.users_user_id')
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->get($this->table);
    }

    /**
     * Update a given user record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data, $where = ['1' => 1]){
        $data['update_time'] = date('y-m-d H:i:s');
        return $this->db
                ->set($data)
                ->where($this->key, $id)
                ->where($where)
                ->update($this->table);
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return boolean deleted or not
     */
    public function delete($id, $where = ['1' => '1']){
        return $this->db
                ->set(['delete_time' => date('Y-m-d H:i:s')])
                ->where($this->key, $id)
                ->where($where)
                ->update($this->table);
    }

}