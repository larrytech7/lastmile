<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class AbstractProviderRequest {

    protected $configs;

    public function __construct($configs){
        $this->configs = $configs;
    }

    public abstract function authorize(array $data = []);

    public abstract function purchase(array $data = []);

    public abstract function refund(array $data = []);

    public abstract function isRedirect();
    
    public abstract function getRedirectUrl();

}