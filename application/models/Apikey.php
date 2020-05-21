<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 1/14/2020
 * Time: 11:23 AM
 */
class Apikey extends CI_Model{

    protected $table = 'api_keys';
    protected $key = 'id';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function getAll(){
        return $this->db->from($this->table)->where('delete_time', NULL)->get();
    }

    public function get($id){
        return $this->db->from($this->table)
        ->where($this->key, $id)
        ->where('delete_time', NULL)
        ->get();
    }
    
    public function getWhere($where){
        return $this->db
            ->select('key, id, application_id, level, payment_provider, payment_callback, payment_status')
            ->from($this->table)
            ->join('applications', 'applications.application_id = api_keys.user_id')
            ->where($where)
            ->where('payments.delete_time', NULL)
            ->get();
    }

    /**
     * insert data
     *
     * @param array $data
     * @return int inserted id
     */
    public function insert($data){
        $this->db->insert($this->table, $data);
    }

    /**
     * insert bulk data
     *
     * @param array $data to insert
     * @return int inserted id
     */
    public function insertBulk($data){
        return $this->db->insert_batch($this->table, $data);
    }
    
    public function update($id, $update){
        $update['update_time'] = date('Y-m-d H:i:s');

        return $this->db
            ->set($update)
            ->where('payment_id', $id)
            ->where('delete_time', NULL)
            ->update($this->table);
    }

    public function delete($id){
        return $this->db
        ->where($this->id, $id)
        ->where('delete_time', NULL)
        ->delete($this->table);
    }

}