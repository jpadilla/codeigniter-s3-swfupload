<?php

class S3 extends Controller {

	function S3()
	{
		parent::Controller();
		$params = array('class' => 's3');
		$this->load->library('tarzan', $params);
	}
	
	function index()
	{
		$this->load->helper('url');
		$s3 = $this->tarzan->s3;
		$data['objects'] = $s3->list_objects($this->config->item('BUCKET'));
		$data['utils'] = $s3->util;
		$this->load->view('s3', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */