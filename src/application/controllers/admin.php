<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function _remap($method, $params = array())
	{
		if (substr($method,0,4) == "ajax")
		{
			$this->load->model('users');
			if (method_exists($this, "_" . $method)) return call_user_func_array(array($this, "_" . $method), $params);
		}
		else
		{
			if (method_exists($this, $method))
			{
				return call_user_func_array(array($this, $method), $params);
			}
		}
		show_404();
	}

	public function index()
	{
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}
		$data['selected_page'] = "admin";
		$this->overview();
	}

	public function overview()
	{
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}

		$data['selected_page'] = "admin";
		$data['page_title'] = "Administration";
		$this->load->model('videos');
		$this->load->model('tags');
		$data['maintag'] = $this->videos->get_from_tag($this->config->item('dropbox_id'),false);
		$data['maintag_count'] = $this->videos->get_tag_count($this->config->item('dropbox_id'));
		$data['maintag_count_wk'] = $this->videos->get_tag_count($this->config->item('dropbox_id'),60*60*24*7);
		$data['maintag_count_day'] = $this->videos->get_tag_count($this->config->item('dropbox_id'),60*60*24);
		$tags = $this->tags->get_featured_tags(0);
		$data['featured_tags'] = array();
		
		foreach ($tags as $tag)
		{
			$tdata = array();
			array_push($tdata,$tag);
			array_push($tdata,$this->videos->get_tag_count($tag->id));
			array_push($tdata,$this->videos->get_from_tag($tag->id,false));
		
			array_push($data['featured_tags'],$tdata);
		}
		$this->load->view('admin/overview',$data);
	}
	
	public function videos($tag = 0)
	{
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}
		$data = array();
		$data['selected_page'] = "admin";
		$data['page_title'] = "Video Administration";
		$data['tag_id'] = $tag;
		$this->load->model('videos');
		$this->load->view('admin/videos',$data);
	}
	
	function check_admin()
	{
		if (!$this->users->isadmin($this->session->userdata('uid')))
		{
			echo("You are not authorized to request this page.");
			return false;
		}
		return true;
	}
	
	/**
	 * Fetches tag for tag tree listing on admin video panel.
	 * ADMIN FUNCTION ONLY.
	 */
	public function _ajax_fetch_tags($parent = 0)
	{
	
		if ($this->check_admin() == false) return;
	
		if ($this->input->get("id"))
			$parent = $this->input->get("id");
		
		//Get the tags
		$this->load->model('tags');
		$tags = $this->tags->fetch_tags($parent,false);
		
		$json = array();
		
		foreach ($tags as $tag)
		{
			$obj = array();
			$obj["data"] = $tag->name;
			$obj["attr"] = array("id" => $tag->id);
			$obj["state"] = "closed";
			$obj["children"] = array();
			array_push($json,$obj);
		}
		echo(json_encode($json));
	}
	
	/**
	 * Fetches video thumbs for listing on admin video panel.
	 * ADMIN FUNCTION ONLY
	 */
	public function _ajax_fetch_videos($parent = 0)
	{
		if ($this->check_admin() == false) return;
		$this->load->model('videos');
		$data['videos'] = $this->videos->get_from_tag($parent,false);
		
		$this->load->view('ajax/admin_video_thumbnails',$data);
	}
	
	public function _ajax_fetch_videos_by_tag($tag,$limit = 32,$start = 0)
	{
		if ($this->check_admin() == false) return;
		$this->load->model('videos');
		$videos = $this->videos->get_from_tag($tag,false,$limit,$start);
		$json = array();
		foreach ($videos as $video)
		{
			$vid = array();
			$vid['name'] = $video->name;
			$vid['author'] = $video->author;
			$vid['identifier'] = $video->identifier;
			$vid['id'] = $video->id;
			array_push($json,$vid);
		}
		echo(json_encode($json));
	}
	
	public function _ajax_tag_properties($tagid)
	{
		if ($this->check_admin() == false) return;
		$this->load->model('tags');
		$tag_info = $this->tags->fetch_tag($tagid);
		$data['tag_info'] = $tag_info;
		$this->load->view('ajax/admin_tag_properties',$data);
	}
	
	public function _build_tag_context($parent_tag,$root = true)
	{
		$this->load->model('tags');
		$tags = $this->tags->fetch_tags($parent_tag, false);
		
		if (count($tags) <= 0) return "";
		
		$retval = "";
		foreach ($tags as $tag)
		{
			$retval .= '"' . $tag->id . '": { "name": "' . $tag->name . '", disabled: false, type: null, className: "tag-' . $tag->id . '",
			callback: function(key, options) { addTag("' . $tag->name . '",key); }';
			$subs = ($this->_build_tag_context($tag->id,false));
			if ($subs != "")
				$retval .= ', "items": {' . $subs . '}';
			$retval .= '},';
		}
		if ($root)
			return substr($retval,0,-1);
		return $retval;
	}
	
	/**
	 * Simple video information editing dialog for users.
	 */
	public function _ajax_editvideo($video_identifier)
	{
		/*
		if ($this->session->userdata('uid') == false)
		{
			echo("You must be signed in to edit video information.");
			return;
		}
		*/
		//Get user information
		$this->load->model('users');
		/*
		if (!$this->users->canedit($this->session->userdata('uid'),$video_identifier))
		{
			echo("You do not have permission to edit this video.");
			return;
		}
		*/
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['title'] = $video_info->name;
		$data['desc'] = $video_info->description;
		$data['thumbnail_time'] = $video_info->thumbnail_time;
		
		$this->load->model('tags');
		$data['tags'] = $this->tags->get_tags_for_video($video_info->id);
		$data['tag_context'] = $this->_build_tag_context(0);
		
		$data['duration'] = $video_info->duration - 1;
		
		
		$this->load->view('ajax/admin_editvideo',$data);
	}
	
	public function _ajax_editvideo_submit($video_identifier)
	{
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		$json = array("success"=>true,"error"=>"");
		
		//Get user information
		$this->load->model('users');
		
		if ($this->session->userdata('uid') == false)
		{
			$json['success'] = false;
			$json['error'] = "You must be signed in to edit video information.";
			echo(json_encode($json));
			return;
		}
		
		if (!$this->users->canedit($this->session->userdata('uid'),$video_identifier))
		{
			$json['success'] = false;
			$json['error'] = "You do not have permission to edit this video.";
			echo(json_encode($json));
			return;
		}
		
		//Get tags and check for permission to use these.
		$tags = $this->input->post('newTag');
		
		/* TODO: Orphaned videos ...?
		if (count($tags <= 0))
		{
			$json['success'] = false;
			$json['error'] = "You must associate at least one tag to each video.";
			echo(json_encode($json));
			return;
		}
		*/
		
		$this->load->model('tags');
		if ($tags != false)
		{
			foreach ($tags as $tag)
			{
				if (!$this->tags->can_upload($this->session->userdata('uid'),$tag))
				{
					$json['success'] = false;
					$json['error'] = "You have selected a tag that you do not have permission to use.";
					echo(json_encode($json));
					return;
				}
				$this->videos->set_video_tag($video_identifier,$tag);
			}
		}
		
		//Delete requested deletion tags...
		unset($tags);
		$tags = $this->input->post('delTag');
		if ($tags != false)
		{
			foreach ($tags as $tag)
			{
				$this->videos->unset_video_tag($video_identifier,$tag);
			}
		}
		
		$this->videos->set_video_title($video_identifier,$this->input->post('title'));
		$this->videos->set_video_description($video_identifier,$this->input->post('desc'));
		if ($video_info->thumbnail_time != $this->input->post('thumb'))
			$this->videos->set_video_thumb($video_identifier,$this->input->post('thumb'));
		$json['success'] = true;
		$json['error'] = "";
		echo(json_encode($json));
		return;
	}
	
	/**
	 * Simple video poster editing dialog for users.
	 */
	public function _ajax_editposter($video_identifier)
	{
		/*
		if ($this->session->userdata('uid') == false)
		{
			echo("You must be signed in to edit video information.");
			return;
		}
		*/
		//Get user information
		$this->load->model('users');
		/*
		if (!$this->users->canedit($this->session->userdata('uid'),$video_identifier))
		{
			echo("You do not have permission to edit this video.");
			return;
		}
		*/
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['title'] = $video_info->name;
		$data['desc'] = $video_info->description;
		$data['thumbnail_time'] = $video_info->thumbnail_time;
		
		$this->load->model('tags');
		$data['tags'] = $this->tags->get_tags_for_video($video_info->id);
		$data['tag_context'] = $this->_build_tag_context(0);
		
		$data['duration'] = $video_info->duration - 1;
		
		$data['poster'] = $this->videos->getPoster($video_info->id);
		
		$this->load->view('ajax/poster_editor',$data);
	}
	
	public function _ajax_editposter_submit($video_identifier,$time,$data)
	{
		$json = array("success"=>true,"error"=>"");
		$this->load->model('users');
		if (!$this->users->canedit($this->session->userdata('uid'),$video_identifier))
		{
			$json['success'] = false;
			$json['error'] = "You do not have permission to edit this video.";
			echo(json_encode($json));
			return;
		}
		
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		
		$this->videos->setPoster($video_info->id,$time,urldecode($data));
		
		echo(json_encode($json));
		return;
	}
	
	
	/**
	 * Simple video embed code dialog
	 */
	public function _ajax_embedcode($video_identifier)
	{
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($video_identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['title'] = $video_info->name;
		$data['desc'] = $video_info->description;
		$data['thumbnail_time'] = $video_info->thumbnail_time;
		
		$this->load->view('ajax/embed_code',$data);
	}
}

?>
