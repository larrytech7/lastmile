<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface ProviderInterface {

    public function authorize(array $data = []);

    public function purchase(array $data = []);

}