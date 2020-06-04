<?php
/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 14/01/2020
 * Time: 9:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Payments extends CI_Migration{

    public function up(){
        $fields = [
            'payment_id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'null' => false
            ],
            'payment_transaction_id' => [
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true
            ],
            'payment_amount' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'payment_provider' => [
                'type' => 'varchar',
                'constraint' => '11',
                'null' => false,
            ],
            'payment_callback' => [
                'type' => 'text',
                'null' => true,
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['SUCCESS', 'PENDING', 'FAILED'],
                'null' => true,
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
            ->add_key('payment_id', true)
            ->create_table('payments', true);
    }

    public function down(){
        $this->dbforge->drop_table('payments');
    }
}