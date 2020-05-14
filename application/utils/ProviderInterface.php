<?php
/**
 * @author Akah <l.akah@sevenadvancedacademy.com>
 * Contributors : 
 */

defined('BASEPATH') OR exit('No direct script access allowed');

interface ProviderInterface {

    public function authorize(array $data = []);

    public function purchase(array $data = []);

    public function refund(array $data = []);

    public function isRedirect();
    
    public function getRedirectUrl();

}