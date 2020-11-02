<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class User extends CI_Model
{
    protected $table = 'users';
    protected $key = "user_id";
    protected $readable_fields = 'user_id, username, name, email, profile_image, user_status, user_phone_number, user_address, role_id, role_name, users.create_time, users.update_time, users.delete_time'; 

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user email for partial authentication
     *
     * @param string $email
     * @param string $password
     * @return object
     */
    public function authenticate($email, $password){

        return $this->db
            ->select($this->readable_fields . ', password')
            ->from($this->table)
            ->join('roles', 'roles.role_id = users.roles_role_id')
            ->where('email', $email)
            ->where('user_status', 'ACTIVE')
            //->where('password', password_hash($password, PASSWORD_BCRYPT))
            ->where('users.delete_time',  NULL)
            ->limit(1)
            ->get();
    }

    /**
     * Get the sum summary for most database entities
     *
     * @param array $where
     * @return array
     */
    public function stats($where = ['1' => '1']){
        $this->db->where($where)->where('delete_time', NULL)->from('users');
        $stats['users'] = $this->db->count_all_results();
        $this->db->where($where)->where(['users.delete_time' => NULL, 'role_name' => 'RESELLER'])->join('roles', 'roles.role_id = users.roles_role_id')->from('users');
        $stats['franchises'] = $this->db->count_all_results();
        $this->db->where($where)->where('clients.delete_time', NULL)->from('clients');
        $stats['clients'] = $this->db->count_all_results();
        $this->db->where($where)->where('rdv.delete_time', NULL)->from('rdv');
        $stats['rdvs'] = $this->db->count_all_results();
        $this->db->where($where)->where('campaigns.delete_time', NULL)->from('campaigns');
        $stats['campaigns'] = $this->db->count_all_results();
        $this->db->where($where)->where('messages.delete_time', NULL)->from('messages');
        $stats['messages'] = $this->db->count_all_results();
        $this->db->where($where)->where('documents.delete_time', NULL)->from('documents');
        $stats['documents'] = $this->db->count_all_results();

        return $stats;
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
    public function get($id){
        return $this->db
            ->select($this->readable_fields)
            ->join('roles', 'roles.role_id = users.roles_role_id')
            ->where($this->key, $id)
            ->where('users.delete_time', NULL)
            ->where('roles.delete_time', NULL)
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
            ->join('roles', 'roles.role_id = users.roles_role_id')
            ->where('users.delete_time', NULL)
            ->where('roles.delete_time', NULL)
            ->get($this->table);
    }

    public function getUserPermissions($role){
        return $this->db
            ->select("permission_id, permission_name, roles.role_id")
            ->join('role_has_permissions', 'role_has_permissions.permissions_permission_id = permissions.permission_id')
            ->join('roles', 'roles.role_id = role_has_permissions.roles_role_id')
            ->where('roles.delete_time', NULL)
            ->where('roles.role_id', $role)
            ->get('permissions');
    }
    /**
     * Update a given user record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data){
        $data['update_time'] = date('y-m-d H:i:s');
        return $this->db
                ->set($data)
                ->where($this->key, $id)
                ->where('delete_time', NULL)
                ->update($this->table);
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return boolean deleted or not
     */
    public function delete($id){
        return $this->db
                ->set(['delete_time' => date('Y-m-d H:i:s')])
                ->where($this->key, $id)
                ->update($this->table);
    }

}