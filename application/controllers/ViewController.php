<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ViewController extends CI_Controller{

	protected $header;
	protected  $footer;
	protected  $body;
	protected $data;
	public $social;

	/**
	 * Constructor to build page views
	 */
	public function __construct($header = 'public/', $footer = 'public/', $main = 'public', $data = []){
		parent::__construct();
		$this->header = $header;
		$this->body = $main;
		$this->footer = $footer;
		$this->data = $data;
	}

	public function loadView(){
		$this->load->view($this->header, $this->data);
		$this->load->view($this->body, $this->data);
		$this->load->view($this->footer, $this->data);
	}

	/**
	 * @return mixed
	 */
	public function getHead()
	{
		return $this->header;
	}

	/**
	 * @param mixed $header
	 */
	public function setHead($header)
	{
		$this->header = $header;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFooter()
	{
		return $this->footer;
	}

	/**
	 * @param mixed $footer
	 */
	public function setFooter($footer)
	{
		$this->footer = $footer;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}


}
