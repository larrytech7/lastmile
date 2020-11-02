<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Tax extends CI_Model
{
    protected $table = 'tax';
    protected $key = "tax_id";
    protected $readable_fields = 'tax_id, tax_name, tax_rate, tax.create_time, tax.update_time, username'; 


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
    public function get($id){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = tax.tax_user_id')    
            ->where('tax_id', $id)
            ->where('tax.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records.
     *
     * @param string tax
     * @return array record(s)
     */
    public function getAll($where = ['1' => '1']){
        return $this->db
            ->join('users', 'users.user_id = tax.tax_user_id')
            ->where('tax.delete_time', NULL)
            ->where($where)
            ->get($this->table);
    }

    /**
     * Update a given tax record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data){
        return $this->db
                ->set($data)
                ->where('tax_id', $id)
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
                    ->where('tax_id', $id)
                    ->update($this->table);
        
    }

}