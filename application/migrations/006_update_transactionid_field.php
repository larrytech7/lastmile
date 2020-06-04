<?php
/**
 * Created by VSCode.
 * User: Akah
 * Date: 03/06/2020
 * Time: 12:30 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_Transactionid_Field extends CI_Migration{

    public function up(){
        $this->modify_transactions_table();
    }

    public function down(){
    }
    
    public function modify_transactions_table(){
        $this->dbforge->drop_column('transactions', 'bill_id');
        $fields = [
            'bill_id' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => false,
                'unique' => false,
                'after' => 'id'
            ]
        ];
        //modify the fields
        $this->dbforge->add_column('transactions', $fields);
    }

    
}