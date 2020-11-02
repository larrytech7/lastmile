<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Setting extends CI_Model
{
    protected $table = 'settings';
    protected $key = "config_id";
    protected $readable_fields = 'config_id, config_commission_type, config_commission_amount, config_telefonist_amount, config_tax_id, config_currency, config_user_id, settings.create_time, settings.update_time'; 

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
     * @param int record_id
     * @return array record
     */
    public function get($id){
        return $this->db
            ->select($this->readable_fields . ', username, name, tax_name, tax_rate')
            ->join('users', 'users.user_id = settings.config_user_id')    
            ->join('tax', 'tax.tax_id = settings.config_tax_id')    
            ->where($this->key, $id)
            ->where('settings.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records.
     *
     * @param string record
     * @return array record(s)
     */
    public function getAll($where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields . ', username, name, tax_name, tax_rate')
            ->join('users', 'users.user_id = settings.config_user_id')
            ->join('tax', 'tax.tax_id = settings.config_tax_id')
            ->where('settings.delete_time', NULL)
            ->where($where)
            ->get($this->table);
    }
    
    public function getPermissions(){
        return $this->db
            ->where('permissions.delete_time', NULL)
            ->get('permissions');
    }

    /**
     * Update a given record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data){
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