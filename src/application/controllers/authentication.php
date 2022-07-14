<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authentication extends MY_Controller {

	public function index()
	{
	}
	
	
	/**
	 * Verifies a log in submitted via AJAX form. Returns response via JSON.
	 */
	public function verify()
	{
		//$this->session->sess_destroy();
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		//Try to get this video's information.
		//print_r($_POST);
		$this->load->model('users');
		$user_info = $this->users->authenticate($email,$password);
		if ($user_info == false)
		{
			$arr = array('error' => "Invalid username or password", 'email' => $email, 'password' => $password);
		} else {
			$user_info = $user_info[0];
			$arr = array('error' => "", 'uid' => $user_info->id);
			$this->session->set_userdata('uid', $user_info->id);
		}
		echo json_encode($arr);
	}
	
	/**
	 * Validate an email address.
	 * Provide email address (raw input)
	 * Returns true if the email address has the email 
	 * address format and the domain exists.
	*/
	function _validEmail($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64)
			{
				// local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255)
			{
				// domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.')
			{
				// local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local))
			{
				// local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
				// character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain))
			{
				// domain part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local)))
			{
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local)))
				{
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
			{
				// domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
	
	/**
	 * Processes a registration form
	 */
	public function register()
	{
		//$this->session->sess_destroy();
		$email = $this->input->post('email');
		$displayName = $this->input->post('displayName');
		$password = $this->input->post('password');
		$confirmPassword = $this->input->post('confirmPassword');

		$this->load->model('users');
		
		
		//Check to see that password and password confirmation are matching
		if ($password != $confirmPassword)
		{
			echo json_encode(array('error' => "Password confirmation does not match."));
			return;
		}

		//Check to see that the e-mail address is valid
		if (!$this->_validEmail($email))
		{
			echo json_encode(array('error' => "E-Mail address provided is invalid."));
			return;
		}
		
		//Check to see that an account with this e-mail doesn't already exist
		if (count($this->users->fetch_author_by_email($email)) > 0)
		{
			echo json_encode(array('error' => "E-Mail address provided is already in use."));
			return;
		}
		
		$activation = $this->_random(6);
		
		$this->users->addUser($email,$password,$displayName,$activation);
		
		$this->_sendEmail("registration",array($email),"LFHS New Media User Account Activation","Greetings,\n\nYou have recently submitted a registration to LFHSNewMedia.com as '" . $displayName . "'.\n\nYour account must be activated before you can log in.\n\nClick the link below to activate your account.\n\n" . $this->config->item('base_url') . "authentication/activate/" . $activation . "\n\nThank you,\nLFHS New Media");
		
		echo json_encode(array('error' => ""));
	}
	
	public function activate($code)
	{
		$this->load->model('users');
		$this->load->helper('url');
		$user = $this->users->fetch_author_by_code($code);
		if (count($user) > 0)
		{
			$user = $user[0];
			$this->users->setActivated($user->id);
			$this->session->set_userdata('uid', $user->id);
			redirect('/profile/me');
		} else {
			show_error("This verification code is not valid, and could not be used.");
		}
	}
	
	public function newpass()
	{
		$this->load->model('users');
		$this->load->helper('url');
		
		$email = $this->input->post('email');
		
		$user = $this->users->fetch_author_by_email($email);
		if (count($user) > 0)
		{
			$user = $user[0];
			$newpass = $this->_random(6);
			$this->users->setAttr($user->id,'password',sha1($newpass));
			$this->_sendEmail("registration",array($email),"LFHS New Media Password","Greetings,\n\nYou have recently submitted a new password request to LFHSNewMedia.com. \n\nYour new password is below:\n\n" . $newpass . "\n\nThank you,\nLFHS New Media");
			echo json_encode(array('error' => ""));
		} else {
			echo json_encode(array('error' => "No account exists with the specified e-mail address."));
		}
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
	
}

?>
