<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function fetch_tag($tagid)
	{
		$this->db->select('id,name,parent,public,path,featured');
		$this->db->distinct();
		$this->db->from('categories');
		$this->db->where('id', $tagid); 
		$query = $this->db->get();
		$tag = $query->result();
		//echo $this->db->last_query();
		return $tag[0];
	}
	
	function fetch_tags($parent = 0, $public = false)
	{
		$this->db->select('id,name,parent,public,path,featured');
		$this->db->distinct();
		$this->db->from('categories');
		if ($public)
			$this->db->where('public', 1); 
		$this->db->where('parent', $parent); 
		$this->db->order_by("name", "asc"); 
		$query = $this->db->get();
		$tags = array();
		foreach ($query->result() as $row)
		{
			array_push($tags,$row);
		}
		//echo $this->db->last_query();
		return $tags;
	}
	
	function get_featured_tags($public = 1)
	{
		$this->db->select('id,name,parent,public,path,featured');
		$this->db->distinct();
		$this->db->from('categories');
		$this->db->where('featured', 1); 
		if ($public == 1)
			$this->db->where('public', 1); 
		$this->db->order_by("id", "asc"); 
		
		$query = $this->db->get();
		
		$tags = array();
		foreach ($query->result() as $row)
		{
			array_push($tags,$row);
		}
		//echo $this->db->last_query();
		return $tags;
	}
	
	function get_tags_for_video($id)
	{
		$this->db->select('categories.id, categories.name, categories.path');
		$this->db->distinct();
		$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		$this->db->where('videos.id', $id); 
		
		$query = $this->db->get();
		
		$tags = array();
		foreach ($query->result() as $row)
		{
			array_push($tags,$row);
		}
		//echo $this->db->last_query();
		return $tags;
	}
	
	function can_upload($userid,$tag)
	{
		$this->load->model('users');
		if ($this->users->isadmin($userid)) return true;
		
		$this->db->select('category_permissions.perm');
		$this->db->distinct();
		//$this->db->from('category_permissions');
		$this->db->from('users');
		$this->db->join('category_permissions', 'category_permissions.groupid = users.usergroup');
		$this->db->join('categories', 'categories.id = category_permissions.catid');
		$this->db->where('users.id = ' . $userid . ' AND category_permissions.perm > 0 AND category_permissions.catid = ' . $tag);  
		$query = $this->db->get();
		$result = $query->result();
		
		if (count($result) > 0) return true;
		return false;
	}
}

?>
