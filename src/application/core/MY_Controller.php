<?php

class MY_Controller extends CI_Controller {
	public $data;
	function MY_Controller ()  {
	        parent::CI_Controller();
	}
	public function __construct()
	{
		parent::__construct();
		$data['background'] = base_url() . "/img/background_generic_01.jpg";
		$data['is_admin'] = false;
		if ($this->session->userdata('uid') != false)
		{
			//Get user information
			$this->load->model('users');
			$user_info = $this->users->fetch_author_by_id($this->session->userdata('uid'));
			$user_info = $user_info[0];
			$data['user_realname'] = $user_info->realname;
			$data['is_admin'] = $this->users->isadmin($this->session->userdata('uid'));
		}
		$this->load->vars($data);
	}
	
	/**
	 * Sends an e-mail using Amazon's SES e-mail service.
	 */
	function _sendEmail($fromName,$toEmailArray,$subject,$message)
	{
		// Include the SDK
		require_once 'misc/aws-sdk/sdk.class.php';

		$amazonSes = new AmazonSES();

		$response = $amazonSes->send_email($fromName . "@lfhsnewmedia.com",
			array('ToAddresses' => $toEmailArray),
			array(
			'Subject.Data' => $subject,
			'Body.Text.Data' => $message,
			)
		);
		if (!$response->isOK())
		{
			// handle error
		}
	}
	
	/**
	 * Generates a random string as user verification.
	 */
	function _random($len) { 
		if (@is_readable('/dev/urandom')) { 
			$f=fopen('/dev/urandom', 'r'); 
			$urandom=fread($f, $len); 
			fclose($f); 
		} 

		$return=''; 
		for ($i=0;$i<$len;++$i) { 
			if (!isset($urandom)) { 
				if ($i%2==0) mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000); 
				$rand=48+mt_rand()%64; 
			} else $rand=48+ord($urandom[$i])%64; 

			if ($rand>57) 
				$rand+=7; 
			if ($rand>90) 
				$rand+=6; 

			if ($rand==123) $rand=45; 
			if ($rand==124) $rand=46; 
			$return.=chr($rand); 
		} 
		return $return; 
	}
}

?>
