<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class AbstractProviderResponse {

    public function getResponseCode(array $data = []){
        
    }

    public abstract function getReponseData();

    public abstract function getError();

    public abstract function setError($error);

    public abstract function setResponseData($data);

}