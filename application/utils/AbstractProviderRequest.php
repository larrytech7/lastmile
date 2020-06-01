<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class AbstractProviderRequest {

    protected $configs;
    protected $eneopay_user;
    protected $eneopay_password;

    public function __construct($configs){
        $this->configs = $configs;
        $this->eneopay_user = $this->config['eneopay_username'];
        $this->eneopay_password = $this->config['eneopay_password'];
    }

    public abstract function authorize(array $data = []);

    public abstract function purchase(array $data = []);

    public abstract function refund(array $data = []);

    public abstract function isRedirect();
    
    public abstract function getRedirectUrl();

    public function setConfigs($config){
        $this->configs = $config;
    }

}