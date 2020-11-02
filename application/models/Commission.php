<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Commission extends CI_Model
{
    protected $table = 'commissions';
    protected $key = "commission_id";
    protected $readable_fields = 'commission_id, commission_amount, commissions.create_time, commissions.update_time, user_id, username, email, name, profile_image, campaign_id, contacts, start_date, end_date, start_time, end_time, prov_tax, fed_tax, tax_amount, tax.status as tax_status '; 

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
            ->join('users', 'users.user_id = commissions.users_user_id')
            ->join('campaigns', 'campaigns.campaign_id = commissions.campaigns_campaign_id')
            ->join('tax', 'tax.tax_id = commissions.tax_tax_id')
            ->where($this->key, $id)
            ->where('users.delete_time', NULL)
            ->where('tax.delete_time', NULL)
            ->where('campaigns.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records. Only user with role admin can read all commissions
     *
     * @param string role
     * @return array record(s)
     */
    public function getAll($where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields)
            ->join('users', 'users.user_id = commissions.users_user_id')
            ->join('campaigns', 'campaigns.campaign_id = commissions.campaigns_campaign_id')
            ->join('tax', 'tax.tax_id = commissions.tax_tax_id')
            ->where('users.delete_time', NULL)
            ->where('tax.delete_time', NULL)
            ->where($where)
            ->where('commissions.delete_time', NULL)
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
    public function delete($id){
        return $this->db
                ->set(['delete_time' => date('Y-m-d H:i:s')])
                ->where($this->key, $id)
                ->update($this->table);
    }

}