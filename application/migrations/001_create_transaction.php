<?php
/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 14/01/2020
 * Time: 9:09 PM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Transaction extends CI_Migration{

    public function up(){
        $fields = [
            'transaction_id' => [
                'type' => 'bigint',
                'constraint' => '11',
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
            ->add_key('transaction_id', true)
            ->create_table('transactions', true);
    }

    public function down(){
        $this->dbforge->drop_table('transactions', true);
    }
}