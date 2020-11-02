<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Document extends CI_Model
{
    protected $table = 'documents';
    protected $key = "document_id";
    protected $readable_fields = 'document_id, document_name, document_type, users_user_id, documents.create_time, documents.update_time, username, name, email, profile_image '; 

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
    public function get($id,$where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = documents.users_user_id')
            ->where($this->key, $id)
            ->where('documents.delete_time', NULL)
            ->where('users.delete_time', NULL)
            ->where($where)
            ->get($this->table);
    }
    
    /**
     * Get all records. Only user with role admin can read all docs
     *
     * @param string role
     * @return array record(s)
     */
    public function getAll($where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = documents.users_user_id')
            ->where('documents.delete_time', NULL)
            ->where('users.delete_time', NULL)
            ->where($where)
            ->get($this->table);
    }

    /**
     * Update a given record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data){
        $data['update_time'] = date('y-m-d H:i:s');
        return $this->db
                ->set($data)
                ->where($this->key, $id)
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