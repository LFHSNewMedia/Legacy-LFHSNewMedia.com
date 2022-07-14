<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function fetch_author_by_id($identifier)
	{
		$this->db->select('id, username, password, realname, email, dateregistered, usergroup, homezip, activationcode');
		$this->db->from('users');
		$this->db->where('id',$identifier);
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result());
	}
	
	function fetch_author_by_code($code)
	{
		$this->db->select('id, username, password, realname, email, dateregistered, usergroup, homezip, activationcode');
		$this->db->from('users');
		$this->db->where('activationcode',$code);
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result());
	}
	
	function fetch_author_by_email($email)
	{
		$this->db->select('id, username, password, realname, email, dateregistered, usergroup, homezip, activationcode');
		$this->db->from('users');
		$this->db->like('email',$email);
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result());
	}
	
	function fetch_usergroup($id)
	{
		$this->db->select('usergroup');
		$this->db->from('users');
		$this->db->where('id',$id);
		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result();
		$result = $result[0];
		$groupid = $result->usergroup;
		return ($groupid);
	}
	
	function fetch_group_name($identifier)
	{
		$this->db->select('name');
		$this->db->from('usergroups');
		$this->db->where('id',$identifier);
		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result();
		$result = $result[0];
		$name = $result->name;
		return $name;
	}
	
	function authenticate($email,$password)
	{
		$this->db->select('id, username, password, realname, email, dateregistered, usergroup, homezip, activationcode');
		$this->db->from('users');
		$this->db->where('email',$email);
		$pw = sha1($password);
		$this->db->where('password',$pw);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($this->db->count_all_results() <= 0)
			return false;
		
		return ($query->result());
	}
	
	/**
	 * Returns true if the user is part of the admin user group, false otherwise.
	 */
	function isadmin($userid)
	{
		$user_info = $this->fetch_author_by_id($userid);
		if (count($user_info) <= 0)
			return false;
		$user_info = $user_info[0];
		//Are we part of the admin group?
		if (count($user_info) > 0 && $user_info->usergroup == $this->config->item('admin_group_id'))
			return true;
		
		return false;
	}
	
	/**
	 * Removes the activation code requirement for the user with the specified id.
	 */
	function setActivated($userid)
	{
		$data = array(
			'activationcode' => ""
		);
		$this->db->where('id', $userid);
		$this->db->update('users', $data);
	}
	
	/**
	 * Returns true if the user has administrator priveledges over the specified video.
	 */
	function canedit($userid,$video_identifier)
	{
		$user_info = $this->fetch_author_by_id($userid);
		if (count($user_info) <= 0)
			return false;
		$user_info = $user_info[0];
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		
		//Is this the author?
		if ($video_info->author == $userid)
			return true;
		
		//Are we part of the admin group?
		if (count($user_info) > 0 && $user_info->usergroup == $this->config->item('admin_group_id'))
			return true;
			
		//Nope, we don't.
		return false;
	}
	
	
	function addUser($email,$password,$displayName,$activation = "")
	{
		$data = array(
			'email' => $email ,
			'realname' => $displayName ,
			'password' => sha1($password) ,
			'dateregistered' => time() ,
			'activationcode' => $activation
		);
		
		$this->db->insert('users', $data); 
	}
	
	function setAttr($uid,$attr,$val)
	{
		$data = array(
			$attr => $val
		);
		$this->db->where('id',$uid);
		$this->db->update('users', $data); 
	}
	
}

?>
