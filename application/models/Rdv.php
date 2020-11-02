<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Rdv extends CI_Model
{
    protected $table = 'rdv';
    protected $key = "rdv_id";
    protected $readable_fields = 'rdv_id, rdv_date, rdv_time, rdv.users_user_id, clients_client_id, rdv.create_time, rdv.update_time, username, name, email, profile_image, first_name, last_name, client_email, client_category, occupation, phone_number'; 

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
        $this->db->trans_begin();
        $inserted = $this->db->insert($this->table, $data);
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }
        return $inserted;
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
            ->join('users', 'users.user_id = rdv.users_user_id')
            ->join('clients', 'clients.client_id = rdv.clients_client_id')
            ->where($this->key, $id)
            ->where($where)
            ->where('rdv.delete_time', NULL)
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->get($this->table);
    }

    public function getWhere($where){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = rdv.users_user_id')
            ->join('clients', 'clients.client_id = rdv.clients_client_id')
            ->where($where)
            ->where('rdv.delete_time', NULL)
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records. Only user with role admin can read all rdv
     *
     * @param string role
     * @return array record(s)
     */
    public function getAll(){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = rdv.users_user_id')
            ->join('clients', 'clients.client_id = rdv.clients_client_id')
            ->where('rdv.delete_time', NULL)
            ->where('users.delete_time', NULL)
            ->where('clients.delete_time', NULL)
            ->get($this->table);
    }

    /**
     * Update a given record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data, $where = ['1' => '1']){
        $data['update_time'] = date('y-m-d H:i:s');
        $this->db->trans_begin();
        $updated = $this->db->set($data)
        ->where($where)
        ->where($this->key, $id)
        ->where('rdv.delete_time', NULL)
        ->update($this->table);
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }
        return $updated;
         
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