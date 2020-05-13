<?php
/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 14/01/2020
 * Time: 9:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Applications extends CI_Migration{

    public function up(){
        $this->create_applications_table();
        $this->create_api_table();
        $this->create_logs_table();
    }

    public function down(){
        $this->dbforge->drop_table('applications');
        $this->dbforge->drop_table('logs');
        $this->dbforge->drop_table('api_keys');
    }

    public function create_api_table(){
        $fields = [
            'id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'auto_increment' => true,
                'null' => false
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => false
            ],
            'level' => [
                'type' => 'int',
                'constraint' => '2',
                'null' => false,
            ],
            'ignore_limits' => [
                'type' => 'tinyint',
                'constraint' => '1',
                'default' => '0',
                'null' => false,
            ],
            'is_private_key' => [
                'type' => 'tinyint',
                'constraint' => '1',
                'default' => '0',
                'null' => false,
            ],
            'date_deleted' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => NULL
            ],
        ];
        //add the fields
        $this->dbforge
            ->add_field('date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP')
            ->add_field('date_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP')
            ->add_field($fields)
            ->add_key('id', true)
            ->create_table('api_keys', true);
    }
    
    public function create_applications_table(){
        $fields = [
            'application_id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'null' => false
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => false
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'application_status' => [
                'type' => 'ENUM',
                'constraint' => ['ACTIVE', 'SUSPENDED'],
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
            ->add_key('application_id', true)
            ->create_table('applications', true);
    }
    
    public function create_logs_table(){
        $fields = [
            'id' => [
                'type' => 'bigint',
                'constraint' => '11',
                'auto_increment' => true,
                'null' => false
            ],
            'uri' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => '7',
                'null' => false
            ],
            'api_key' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => false
            ],
            'params' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'ip_address' => [
                'type' => 'varchar',
                'constraint' => '45',
                'null' => false,
            ],
            'time' => [
                'type' => 'int',
                'constraint' => '11',
                'null' => false,
            ],
            'rtime' => [
                'type' => 'float',
                'null' => true,
            ],
            'authorized' => [
                'type' => 'varchar',
                'constraint' => '1',
                'null' => false,
            ],
            'response_code' => [
                'type' => 'tinyint',
                'constraint' => '3',
                'default' => '0',
            ]
        ];
        //add the fields
        $this->dbforge
            ->add_field($fields)
            ->add_key('id', true)
            ->create_table('logs', true);
    }

    
}