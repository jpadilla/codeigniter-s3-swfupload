<?php
class Upload extends Controller {

	function Upload()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$data['AWS_ACCESS_KEY_ID'] = $this->config->item('AWS_ACCESS_KEY_ID');
		$data['AWS_SECRET_ACCESS_KEY'] = $this->config->item('AWS_SECRET_ACCESS_KEY');
		$data['BUCKET'] = $this->config->item('BUCKET');
		$data['SUCCESS_REDIRECT'] = $this->config->item('SUCCESS_REDIRECT');
		$data['SWFRoot'] = $this->config->item('SWFRoot');
		
		$this->load->view('upload', $data);
	}
	
	function success()
	{
		$data['AWS_ACCESS_KEY_ID'] = $this->config->item('AWS_ACCESS_KEY_ID');
		$data['AWS_SECRET_ACCESS_KEY'] = $this->config->item('AWS_SECRET_ACCESS_KEY');
		$data['BUCKET'] = $this->config->item('BUCKET');
		$data['SUCCESS_REDIRECT'] = $this->config->item('SUCCESS_REDIRECT');
		$data['SWFRoot'] = $this->config->item('SWFRoot');
		
		$this->load->view('success', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */