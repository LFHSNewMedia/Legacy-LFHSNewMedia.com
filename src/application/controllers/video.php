<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data['selected_page'] = "videos";
		$data['page_title'] = "Video";
		
		$base_path = $this->config->item('base_path');
		
		$this->load->model('videos');
		$slide_videos = $this->videos->get_popular_of_tag(0,20);
		$data['popular_slider_json'] = array();
		
		foreach($slide_videos as $video)
		{
			$slide = array();
			$slide['title'] = $video->name;
			$slide['identifier'] = $video->identifier;
			array_push($data['popular_slider_json'],$slide);
		}
		$data['popular_slider_json'] = str_replace("'","\\'",json_encode($data['popular_slider_json']));
		
		$this->load->model('tags');
		$featured_tags = $this->tags->get_featured_tags();
		$features = array();
		foreach ($featured_tags as $feature)
		{
			$featureid = $feature->id;
			$featurevids = $this->videos->get_latest_of_tag($featureid,10);
			array_push($features, array($feature,$featurevids));
		}
		
		$data['features'] = $features;
		
		$this->load->view('video_list',$data);
	}
	
	public function embed($identifier)
	{
		
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['video_id'] = $video_info->id;
		$data['video_title'] = $video_info->name;
		$data['page_title'] = $video_info->name;
		$data['video_description'] = $video_info->description;
		$data['video_authorid'] = $video_info->author;
		$data['video_timeadded'] = $video_info->timeadded;
		$data['video_viewcount'] = $video_info->viewcount;
		
		$this->load->view('embed',$data);
	}
	
	public function play($identifier)
	{
		$data['selected_page'] = "videos";
		$data['page_title'] = "Video";
		
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['video_id'] = $video_info->id;
		$data['video_title'] = $video_info->name;
		$data['page_title'] = $video_info->name;
		$data['video_description'] = $video_info->description;
		$data['video_authorid'] = $video_info->author;
		$data['video_timeadded'] = $video_info->timeadded;
		$data['video_viewcount'] = $video_info->viewcount;
		
		
		//Get tag listing
		$this->load->model('tags');
		$tag_list = $this->tags->get_tags_for_video($video_info->id);
		$data['tag_list'] = array();
		foreach ($tag_list as $tag)
		{
			array_push($data['tag_list'],array($tag->id,$tag->name,$tag->path));
		}
		
		//Get author information
		$this->load->model('users');
		$author_info = $this->users->fetch_author_by_id($video_info->author);
		$author_info = $author_info[0];
		
		$data['video_author'] = $author_info->realname;
		$data['video_canedit'] = $this->users->canedit($this->session->userdata('uid'),$identifier);
		
		//Set background image...
		$data['background'] = site_url("video/background/" . $video_info->identifier);
		$this->load->view('player',$data);
	}
	
	public function preview($identifier)
	{
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		
		$data['video_identifier'] = $video_info->identifier;
		$data['video_id'] = $video_info->id;
		$data['video_title'] = $video_info->name;
		$data['video_description'] = $video_info->description;
		$data['video_authorid'] = $video_info->author;
		$data['video_timeadded'] = $video_info->timeadded;
		$data['video_viewcount'] = $video_info->viewcount;
	
		$this->load->view('ajax/preview_video',$data);
	}
	
	public function thumb($identifier,$size = "1280x720",$ttime = "",$forcegen = false)
	{
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		if ($ttime == "")
			$thumbnail_time = $video_info->thumbnail_time;
		else
			$thumbnail_time = $ttime;
		$id = $video_info->id;
		$uri = "http://" . $this->config->item('cloudfront_download') . "/" . $this->config->item('s3_video_keyprefix') . $identifier . ".mp4";
		
		//Check if it's a standard (cached) size
		if (!$forcegen && ($size == "150x84" || $size == "180x100" || $size == "214x120" || $size == "640x360" || $size == "856x480" || $size == "1280x720" || $size == "1920x1080"))
		{
			// If we don't have a cached copy, generate one.
			if (!file_exists($this->config->item('base_path') . "/images/thumbs/" . $size . "/" . $id . ".jpg"))
			{
				$getThumb = $this->videos->getThumb($uri, $thumbnail_time);
				$image_handle = $getThumb[0];
				$fullSize = $getThumb[1];
				$sizes = explode("x",$size);
				$tnImage = imagecreatetruecolor($sizes[0], $sizes[1]);
				imagecopyresampled($tnImage,$image_handle,0,0,0,0,$sizes[0],$sizes[1],$fullSize[0],$fullSize[1]);
				imagejpeg($tnImage,$this->config->item('base_path') . "/images/thumbs/" . $size . "/" . $id . ".jpg");
			}
			//Display it.
			header('Content-type: image/jpeg');
			readfile($this->config->item('base_path') . "/images/thumbs/" . $size . "/" . $id . ".jpg");
		} else
		{
			$getThumb = $this->videos->getThumb($uri, $thumbnail_time);
			$image_handle = $getThumb[0];
			$fullSize = $getThumb[1];
			$sizes = explode("x",$size);
			$tnImage = imagecreatetruecolor($sizes[0], $sizes[1]);
			imagecopyresampled($tnImage,$image_handle,0,0,0,0,$sizes[0],$sizes[1],$fullSize[0],$fullSize[1]);
			header('Content-type: image/jpeg');
			imagejpeg($tnImage);
		}
	}
	
	public function poster($identifier,$size = "1920x1080",$preview = false,$time = -1,$parameters = "")
	{
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		
		if ($time == -1)
			$thumbnail_time = $video_info->thumbnail_time;
		else
			$thumbnail_time = $time;
		$id = $video_info->id;
		
		$uri = "http://" . $this->config->item('cloudfront_download') . "/" . $this->config->item('s3_video_keyprefix') . $identifier . ".mp4";
		
		if ($parameters != "")
		{
			$poster_info =  urldecode($parameters);
			//print_r($parameters);
			//print_r($poster_info);
		}
		else
		{
			$poster_info = $this->videos->getPoster($id);
			if (count($poster_info) > 0)
				$poster_info = $poster_info[0]->data;
			else
				$poster_info = "";
		}
		//Check if it's a standard (cached) size and is pre-generated
		if (($size == "1280x720" || $size == "1920x1080") && file_exists($this->config->item('base_path') . "/images/posters/" . $size . "/" . $id . ".jpg") && !$preview)
		{
			//Display it.
			header('Content-type: image/jpeg');
			readfile($this->config->item('base_path') . "/images/posters/" . $size . "/" . $id . ".jpg");	
		} else
		//No cached copy available.
		{
			//Generate it.
			$getThumb = $this->videos->getThumb($uri, $thumbnail_time);
			$image_handle = $getThumb[0];
			$fullSize = $getThumb[1];
			$sizes = explode("x",$size);
			$tnImage = imagecreatetruecolor($sizes[0], $sizes[1]);
			imagecopyresampled($tnImage,$image_handle,0,0,0,0,$sizes[0],$sizes[1],$fullSize[0],$fullSize[1]);
			
			//Get text samples from poster data.
			if ($poster_info != "")
			{
				//$poster_info = $poster_info[0];
				$text_samples = explode("@@",$poster_info);
				foreach ($text_samples as $text_sample)
				{
					$text_data = explode("||",$text_sample);
					$text_x = $text_data[0] / 100;
					$text_y = $text_data[1] / 100;
					$text_size = $text_data[2] * ($sizes[0] / 1920);
					$text_text = $text_data[3];
					$text_color = $text_data[4];
					$text_color = sscanf($text_color, '#%2x%2x%2x');
					$text_font_color = imagecolorallocate($tnImage,$text_color[0],$text_color[1],$text_color[2]);
					//Time to write the poster text.
					$font = $this->config->item('base_path') . '/fonts/Aaargh.ttf';
					$tb = imagettfbbox($text_size, 0, $font, $text_text);
					imagettftext($tnImage, $text_size, 0, ($text_x*$sizes[0])-($tb[2]/2), ($text_y*$sizes[1])-($tb[5]/2), $text_font_color, $font, $text_text);
				}
			}
			header('Content-type: image/jpeg');
			imagejpeg($tnImage);
			//If it's a standard size, cache it.
			if ($size == "1280x720" || $size == "1920x1080")
				imagejpeg($tnImage,$this->config->item('base_path') . "/images/posters/" . $size . "/" . $id . ".jpg");
		}
	}
	
	public function background($identifier)
	{
		//Try to get this video's information.
		$this->load->model('videos');
		$video_info = $this->videos->fetch_video($identifier);
		$video_info = $video_info[0];
		
		$thumbnail_time = $video_info->thumbnail_time;
		$id = $video_info->id;
		
		$uri = "http://" . $this->config->item('cloudfront_download') . "/" . $this->config->item('s3_video_keyprefix') . $identifier . ".mp4";
		
		header('Content-type: image/jpeg');
	
		if (!file_exists($this->config->item('base_path') . "/images/thumbs/backgrounds/" . $id . ".jpg"))
		{
			
			$image = $this->videos->getThumb($uri, $thumbnail_time);
			$image = $image[0];
			
			/**
			/* Image blurring is now done on the client-side to reduce server load.
			
			//3rd parameter accepts the brightness level.
			$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
	
			imagefilter($image, IMG_FILTER_BRIGHTNESS, -130);
			//imageconvolution($image, $gaussian, 16, 0);
			for ($i=0;$i<20;$i++)
				imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
			*/
			imagejpeg($image, $this->config->item('base_path') . "/images/thumbs/backgrounds/" . $id . ".jpg", 85);
			imagedestroy($image);
		}
		readfile($this->config->item('base_path') . "/images/thumbs/backgrounds/" . $id . ".jpg");
	
	}
	
	public function rate($identifier)
	{
	
		echo("TO BE IMPLEMENTED");
	
	}
	
	
	public function ajax_search_menu()
	{
		$this->load->view('ajax/search_panel');
	}
	
	public function ajax_search_results($keywords)
	{
		$this->load->model('videos');
		$this->load->model('users');
		if ($this->session->userdata('uid') != false)
			$usergroup = $this->users->fetch_usergroup($this->session->userdata('uid'));
		else
			$usergroup = 0;
		$results = $this->videos->get_all_videos_permission($usergroup,0,$keywords);
		$json['results'] = array();
		foreach($results as $video)
		{
			$entry = array();
			$entry['title'] = $video->name;
			$entry['identifier'] = $video->identifier;
			$entry['author'] = $video->realname;
			$entry['duration'] = $video->duration;
			array_push($json['results'],$entry);
		}
		echo(json_encode($json));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
