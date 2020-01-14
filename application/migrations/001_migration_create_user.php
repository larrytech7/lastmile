<?php
/**
 * Created by PhpStorm.
 * User: Vanessa
 * Date: 14/01/2020
 * Time: 9:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_User extends CI_Migration{

    public function up(){
        $fields = [
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'user_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ],
            'user_profile_pic' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'user_social_linkedin' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'user_social_twitter' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'user_social_facebook' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
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
            ->add_key('user_id', true)
            ->create_table('users', true);

    }

    public function down(){

    }
}