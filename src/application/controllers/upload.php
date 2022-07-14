<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends MY_Controller {

	public function index()
	{
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}
		$data['page_title'] = "Upload Video";
		$data['selected_page'] = "upload";
		$this->load->view('upload_form',$data);
	}
	
	public function process()
	{
		$json = array("success" => true, "msg" => "");
		if ($this->session->userdata('uid') == false)
		{
			redirect(site_url("profile/login"));
			return;
		}
		$this->load->model('videos');
		$cats = array();
		$video_id = $this->videos->queue_video($this->session->userdata('uid'),$_FILES["videoFile"]["name"]);
		if ($video_id != false)
		{
			if (move_uploaded_file($_FILES["videoFile"]["tmp_name"],$this->config->item('base_path') . "/tmp/video/" . $video_id . ""))
			{
				$cmd = "/usr/bin/php " . $this->config->item('base_path') . "/index.php upload queue_process " . $video_id . " &";
				Proc_Close (Proc_Open ($cmd, Array (), $foo));
				echo(json_encode($json));
				return;
			} else {
				$json['success'] = false;
				$json['msg'] .= "<br />Could not move uploaded file to temporary storage.";
			}
		
		} else {
			$json['success'] = false;
			$json['msg'] .= "<br />Could not insert video queue information into database.";
		}
		echo(json_encode($json));
	}
	
	
	/**
	 * Automated task to be executed on the CLI.
	 */
	public function queue_process($videoid)
	{
		//Check that this is being run server-side
		if (php_sapi_name() != "cli")
		{
			echo("Error! This can only be run server-side via the PHP CLI.");
			return;
		}
		
		// Check that the video file is valid.
		$this->load->model('videos');
		$this->videos->set_queue_video_status($videoid,"Verifying video format...");
		$fileName = $this->config->item('base_path') . "/tmp/video/" . $videoid;
		$video_info = $this->videos->video_information($fileName);
		if ($video_info['vcodec'] != 'h264')
		{
			$this->videos->set_queue_video_status($videoid,"Invalid video codec. H264 is required.",-1);
			unlink($fileName);
			return;
		}
		if ($video_info['bitrate'] > 10000)
		{
			$this->videos->set_queue_video_status($videoid,"Excessive bit rate of over 10 mbps.",-1);
			unlink($fileName);
			return;
		}
		
		// Get queued item info
		$queue_info = $this->videos->fetch_queue_item($videoid);
		
		// Include the SDK
		require_once 'misc/aws-sdk/sdk.class.php';
		
		// Instantiate the class
		$s3 = new AmazonS3();
		
		$this->videos->set_queue_video_status($videoid,"Uploading to Amazon Cloud Storage...",0);
		
		// 1. Create object from a file.
		$response = $s3->create_object(
			$this->config->item('s3_bucketname'),
			$this->config->item('s3_video_keyprefix') . $videoid . ".mp4",
			array('fileUpload' => $fileName,'acl' => AmazonS3::ACL_PUBLIC)
		);

		// Success?
		print_r($response->isOK());
		if ($response->isOK())
		{
			$this->videos->set_queue_video_status($videoid,"Upload to cloud storage successful. Updating database entry...",0);
			$ffmpeg_info = $this->videos->video_information($fileName);
			$this->videos->add_video($videoid,$queue_info->userid,$queue_info->name,"",array(),$ffmpeg_info['duration']);
			$this->videos->set_queue_video_status($videoid,"Video upload successful.",1);
			unlink($fileName);
		} else {
			$this->videos->set_queue_video_status($videoid,"Error uploading to cloud storage.",-1);
			unlink($fileName);
			return;
		}
	}
	
	public function madgicke()
	{
		$this->load->model('videos');
		$this->videos->add_video($this->input->post('videoid'),$this->input->post('userid'),$this->input->post('name'),$this->input->post('desc'),array(),0);
		$this->videos->set_video_timeadded($this->input->post('videoid'),$this->input->post('timeadded'));
		
		$mysql_con = mysql_connect("localhost","lfhstele","abc12345de");
		mysql_select_db("lfhstele_telecom",$mysql_con);
		$tag_query = mysql_query("SELECT * FROM  `tags` WHERE  `vidID` = " . $this->input->post('origid'),$mysql_con);
		while ($tag_result = mysql_fetch_array($tag_query))
		{
			$this->videos->set_video_tag($this->input->post('videoid'),$tag_result[2]);
		}
	}
}

?>
