 <?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Tarzan
{
	function __construct($params)
	{
		ini_set('include_path', ini_get('include_path').';'.PATH_SEPARATOR . APPPATH . 'libraries/tarzan_2.0.5');
		$this->ci =& get_instance();
		$key = $this->ci->config->item('AWS_ACCESS_KEY_ID');
 		$secret = $this->ci->config->item('AWS_SECRET_ACCESS_KEY');
		$account_id = $this->ci->config->item('AWS_ACCOUNT_ID');
		$cannonical_id = $this->ci->config->item('AWS_CANNONICAL_ID');
		require_once('tarzan_2.0.5/tarzan.class.php');
		/* $params['class'] is the name of AWS services found in the Tarzan library, examples are: ec2, sdb..etc... You may need to add other parameters when you initialize those classes.*/
		if($params['class'] == 's3')
		{
			$this->s3 = new AmazonS3($key, $secret);
		}
	}
}