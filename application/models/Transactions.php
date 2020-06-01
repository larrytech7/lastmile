<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 1/14/2020
 * Time: 11:23 AM
 */
class Transactions extends CI_Model{

    protected $table = 'transactions';
    protected $key = 'transactions_id';

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
        return $this->db->from($this->table)
        ->where($where)
        ->where('delete_time', NULL)
        ->get();
    }
    
    public function updateWhere($where, $set){
        return $this->db
            ->set($set)
            ->where($where)
            ->where('delete_time', NULL)
            ->update($this->table);
    }

    /**
     * insert provider data
     *
     * @param array $data
     * @return int inserted id
     */
    public function insert($data){
        return $this->db->insert($this->table, $data);
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

    public function delete($id){
        return $this->db
        ->where($this->id, $id)
        ->where('delete_time', NULL)
        ->delete($this->table);
    }

    /**
     * Checks the integrity of the transaction to ensure that the total amount sent is the total amount received for the transaction
     *
     * @param string $tid
     * @param integer $total
     * @return boolean
     */
    public function validate($tid, $total){
        if(empty($total) || $total == 0){
            return false;
        }
        $sum = $this->db
            ->from($this->table)
            ->where('ext_transaction_id', $tid)
            ->select_sum('transaction_amount', 'total_amount')
            ->get();

        return ($sum->num_rows() > 0) && $sum->result_object()[0]->total_amount == $total;
    }

    /**
     * Empty transactions table
     *
     * @return void
     */
    public function clear(){
        return $this->db->empty_table($this->table);
    }

}