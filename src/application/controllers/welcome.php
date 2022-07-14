<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

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
		$this->data['selected_page'] = "landing";
		$this->data['page_title'] = "Welcome";
		$this->data['slide_interval'] = 7000;
		$this->data['transition_speed'] = 1000;
		
		$this->data['noOverlay'] = true;
		
		$base_path = $this->config->item('base_path');
		
		$this->load->model('videos');
		$slide_videos = $this->videos->get_latest_of_tag(0,10);
		$this->data['slides'] = array();
		
		foreach($slide_videos as $video)
		{
			$slide = array();
			$slide['image'] = site_url("video/poster/" . $video->identifier . "/1920x1080");
			$slide['title'] = $video->name;
			$slide['thumb'] = site_url("video/thumb/" . $video->identifier . "/150x84");
			$slide['url'] = site_url("video/play/" . $video->identifier);
			array_push($this->data['slides'],$slide);
		
		}
		/*
		$testslide['image'] = "img/test_poster.jpg";
		$testslide['title'] = "MidWest: a 48 hour film production. Runner-up best film.";
		$testslide['thumb'] = "img/test_poster_thumb.jpg";
		$testslide['url'] = "#";
		
		array_push($this->data['slides'],$testslide);
		*/
		
		$this->load->view('landing_page',$this->data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
