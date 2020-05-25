<?php
/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 14/01/2020
 * Time: 9:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_Transactions extends CI_Migration{

    public function up(){
        //TODO: Add method to backup table data
        $this->create_transactions_table();
        $this->update_transactions_table();
        $this->modify_transactions_table();
        //TODO : Add method to repopulate table
    }

    public function down(){
        $this->dbforge->drop_table('transactions');
    }

    public function create_transactions_table(){
        $fields = [
            'id' => [
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => true,
                'null' => false
            ],
            'transaction_id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'null' => false
            ],
            'ext_transaction_id' => [
                'type' => 'varchar',
                'constraint' => '128',
                'null' => false
            ],
            'transaction_amount' => [
                'type' => 'bigint',
                'constraint' => '11',
                'null' => false
            ],
            'delete_time' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => NULL
            ],
        ];
        //add the fields
        $this->dbforge
            ->add_field('create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP')
            ->add_field('update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP')
            ->add_field($fields)
            ->add_key('id', true)
            ->create_table('transactions', true);
    }

    public function update_transactions_table(){
        $fields = [
            'transaction_status' => [
                'type' => 'ENUM',
                'constraint' => ['SUCCESS', 'PROCESSING', 'FAILED'],
                'default' => 'PROCESSING'
            ]
        ];
        //add the fields
        $this->dbforge->add_column('transactions', $fields);
    }
    
    public function modify_transactions_table(){
        $fields = [
            'transaction_id' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => false,
                'unique' => true
            ]
        ];
        //modify the fields
        $this->dbforge->modify_column('transactions', $fields);
    }

    
}