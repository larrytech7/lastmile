<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Akah  <l.akah@sevenadvancedacademy.com>
 * Date: 15/08/2020
 * Time: 11:23 AM
 */
class Campaign extends CI_Model
{
    protected $table = 'campaigns';
    protected $key = "campaign_id";
    protected $readable_fields = 'campaign_id, start_time, start_date, end_time, end_date, campaigns.users_user_id, campaigns.create_time, campaigns.update_time, contacts '; 


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
        $inserted = false;
        //get active tax regime
        $config = $this->db->where('config_id', $data['config'])->limit(1)->get('settings')->result_object();
        if(count($config) > 0){
            //there's an active tax we can apply, 
            unset($data['config']);
            $inserted = $this->db->insert($this->table, $data); //inserted campaign, now insert commission
            $insert_id = $this->db->insert_id();
            $config_data = $config[0];
            $this->db->insert('commissions', [
                'commission_amount' => $config_data->config_commission_amount,
                'tax_tax_id' => $config_data->config_tax_id,
                'campaigns_campaign_id' => $insert_id,
                'users_user_id' => $data['users_user_id']
            ]);
        }
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
            ->select($this->readable_fields . ', first_name, last_name, client_category, client_id, commission_id, commission_amount, fed_tax, prov_tax, tax_amount')
            ->join('users', 'users.user_id = campaigns.users_user_id')
            ->join('clients', 'clients.client_id = campaigns.campaign_client_id')
            ->join('commissions', 'commissions.campaigns_campaign_id = campaigns.campaign_id')
            ->join('tax', 'tax.tax_id = commissions.tax_tax_id')
            ->where($this->key, $id)
            ->where($where)
            ->where('users.delete_time', NULL)
            ->where('campaigns.delete_time', NULL)
            ->get($this->table);
    }
    
    /**
     * Get all records. Only user with role admin can read all users
     *
     * @param string role where clause
     * @return array record(s)
     */
    public function getAll($where = ['1' => '1']){
        return $this->db
            ->select($this->readable_fields . ', username, name, first_name, last_name')
            ->join('users', 'users.user_id = campaigns.users_user_id')
            ->join('clients', 'clients.client_id = campaigns.campaign_client_id')
            ->where($where)
            ->where('users.delete_time', NULL)
            ->where('campaigns.delete_time', NULL)
            ->get($this->table);
    }

    /**
     * Update a given user record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data, $where = ['1' => '1']){
        $data['update_time'] = date('y-m-d H:i:s');
        return $this->db
                ->set($data)
                ->where($where)
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
                ->where($where)
                ->where($this->key, $id)
                ->update($this->table);
    }

}