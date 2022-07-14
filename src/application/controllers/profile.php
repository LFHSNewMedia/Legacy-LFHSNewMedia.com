<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends MY_Controller {

	public function index()
	{
		$this->me();
	}
	
	public function login($error = "")
	{
		$data['error'] = $error;
		$data['page_title'] = "Log In";
		$this->load->view('login',$data);
	}
	
	public function auth()
	{
	
	
	
	}
	
	public function me()
	{
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}
		//Get user information
		$this->load->model('users');
		$user_info = $this->users->fetch_author_by_id($this->session->userdata('uid'));
		$user_info = $user_info[0];
		
		$this->load->model('videos');
		$random_video = $this->videos->get_random_from_user($user_info->id);
		if (count($random_video) > 0)
		{
			$random_video = $random_video[0];
			$data['background_id'] = $random_video->identifier;
		} else {
		}
		$data['selected_page'] = "profile";
		$data['page_title'] = $user_info->realname;
		$data['user_displayname'] = $user_info->realname;
		$data['user_username'] = $user_info->username;
		$data['user_groupname'] = $this->users->fetch_group_name($user_info->usergroup);
		$data['user_joindate'] = $user_info->dateregistered;
		$data['user_videosuploaded'] = $this->videos->fetch_user_video_count($user_info->id);
		
		$data['user_videos'] = $this->videos->fetch_videos_by_user($user_info->id);
		
		$this->load->view('profile',$data);
	}
	
}

?>
