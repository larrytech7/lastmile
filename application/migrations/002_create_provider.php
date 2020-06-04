<?php
/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 14/01/2020
 * Time: 9:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Provider extends CI_Migration{

    public function up(){
        $fields = [
            'provider_id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'auto_increment' => true,
                'null' => false
            ],
            'provider_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'provider_logo' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'provider_short_tag' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'unique' => true,
                'null' => true,
            ],
            'provider_status' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
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
            ->add_key('provider_id', true)
            ->create_table('providers', true);
    }

    public function down(){
        $this->dbforge->drop_table('providers', true);
    }
}