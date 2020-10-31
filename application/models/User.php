<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Akah
 * Date: 1/14/2020
 * Time: 11:23 AM
 */
class User extends CI_Model
{
    protected $table = 'users';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

}