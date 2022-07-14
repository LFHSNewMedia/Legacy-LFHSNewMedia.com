<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * PHP Class to collect information about Video
 *
 *  author: Abramov Alexander (wl)
 *  web-site: http://www.neotemple.ru/
 *
 **/

class phpffmpeg {

// Bitrate
	var $bitrate = 0;

// Duration
	var $duration = 0;
	var $duration_raw = "";

// Codec Name
	var $vcodec = "";

	var $w = 0;
	var $h = 0;

	var $framerate = "";

	var $acodec = "";
	var $audiorate = "";
	var $channels = "";
	var $audiobitrate = "";

// Construct
	function phpffmpeg( $file ) {
	
	// Global Array of Information
		$lOut = array();

	// Escape a string to be used as a shell argument (* thx to Alexey Zakhlestin *)
		$file = escapeshellarg($file);
	
		$cmd = "ffmpeg -i ".$file." 2>&1";
		exec($cmd, $lOut);

	// We are getting duration of movie
		$lDuration = $this->getLine( '/Duration: (.*?),/', $lOut );
		if (count($lDuration) < 2) return false;
		$lArray = explode(':', $lDuration[1]);
		if (count($lArray) < 3) return false;
		$this->duration = intval($lArray[0] * 3600 + $lArray[1] * 60 + round($lArray[2])); // round for not float
		$this->duration_raw = $lArray[0].":".$lArray[1].":".round($lArray[2]); // raw txt format of time
	
	// Getting Bitrate
		$lBitrate =  $this->getLine( '/, bitrate: (\S+) kb\/s/', $lOut );
	
		$this->bitrate = intval($lBitrate[1]); // get only digits
	
	// Video information
		$lVideoinfo =  $this->getLine( '/Stream #\d+.\d+.*?: Video: (\S+)\, .*?\, (\d+)x(\d+)(,)? .*?\, (.*?) tbr/', $lOut );

		// Codec
		$this->vcodec = $lVideoinfo[1];
		$this->w = intval($lVideoinfo[2]);
		$this->h = intval($lVideoinfo[3]);
		$this->framerate = $lVideoinfo[4];
	
	
	// Audio information
		$lAudioinfo =  $this->getLine( '/Stream #\d+.\d+.*?: Audio: (.*?), (.*?) Hz, (\S+).*?\, .*?\, (\S+) kb/', $lOut );
	
		$this->acodec = $lAudioinfo[1];
		$this->audiorate = $lAudioinfo[2];
		$this->channels = $lAudioinfo[3];
		$this->audiobitrate = $lAudioinfo[4];
	}

/*
 ******************************************
 * Name: getLine()
 *  $inMatch - regexp
 *  &$inArray - global array of data
 *
 * Desc: Search regexp in array
 ******************************************
 */
	function getLine( $inMatch, &$inArray ) {
	
		reset($inArray);
	
		$fResult = array();
	
		$i = 0;
	
		while(($lArr = next($inArray)) !== false) {
			if( preg_match($inMatch, $lArr, $fResult) ) {
				return $fResult;
			}
			$i++;
		}
	
	// No matches found
		return 0;
	}

}


class Videos extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function get_latest_of_tag($tagid=0,$number = 10)
	{
		$this->db->select('videos.id, videos.author, videos.name, categories.public, videos.identifier');
		$this->db->distinct();
		$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		$this->db->where('categories.public', 1); 
		$this->db->like('categories.path', '/' . $tagid . '/'); 
		$this->db->order_by("videos.timeadded", "desc"); 
		$this->db->limit($number);
		
		$query = $this->db->get();
		
		$videos = array();
		foreach ($query->result() as $row)
		{
			array_push($videos,$row);
		}
		//echo $this->db->last_query();
		return $videos;
	}
	
	function get_popular_of_tag($tagid=0,$number = 10,$timeframe = 1900800,$numberAsMinimum = true) //1900800 = 22 days
	{
		$minimumTime = (time()-$timeframe);
		//Get a minimum time frame based on number...
		if ($numberAsMinimum)
		{
			$this->db->select('videos.timeadded');
			$this->db->distinct();
			$this->db->from('videos');
			$this->db->join('tags', 'tags.vidID = videos.id');
			$this->db->join('categories', 'categories.id = tags.tagID');
			$this->db->where('categories.public', 1); 
			$this->db->like('categories.path', '/' . $tagid . '/'); 
			$this->db->order_by("videos.timeadded", "desc"); 
			$this->db->limit(1,$number);
			$query = $this->db->get();
			$lastVid = $query->result();
			$numTime = $lastVid[0]->timeadded;
		
			//If we cannot possibly get that many vides within the time frame...
			if ($minimumTime > $numTime)
				$minimumTime = $numTime; //Shift the time frame back to fill.
		}
		
		$this->db->select('videos.id, videos.author, videos.name, categories.public, videos.identifier');
		$this->db->distinct();
		$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		$this->db->where('categories.public', 1); 
		$this->db->where('videos.timeadded >=', $minimumTime); 
		$this->db->like('categories.path', '/' . $tagid . '/'); 
		$this->db->order_by("videos.viewcount", "desc"); 
		$this->db->limit($number);
		
		$query = $this->db->get();
		
		$videos = array();
		foreach ($query->result() as $row)
		{
			array_push($videos,$row);
		}
		return $videos;
	}
	
	function get_from_tag($tagid=0,$public = true,$limit = 32,$start = 0)
	{
		$this->db->select('videos.id, videos.author, videos.name, categories.public, tags.tagID, videos.identifier');
		$this->db->distinct();
		//$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		if ($public)
			$this->db->where('categories.public', 1); 
		$this->db->where('tags.tagID', $tagid); 
		$this->db->order_by("videos.timeadded", "desc"); 
		
		$query = $this->db->get('videos',$limit,$start);
		
		$videos = array();
		foreach ($query->result() as $row)
		{
			array_push($videos,$row);
		}
		//echo $this->db->last_query();
		return $videos;
	}
	
	function get_tag_count($tagid,$time = 0)
	{
		$this->db->select('COUNT(videos.id) as count');
		if ($time > 0)
			$this->db->where('videos.timeadded >', time() - $time); 
		
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->where('tags.tagID', $tagid); 
		$query = $this->db->get('videos');
		$result = $query->result();
		$result = $result[0];
		return $result->count;
	}
	
	function get_all_videos_permission($groupid = 0,$tagid = 0,$nameQuery = "")
	{
		$nameQuery = str_replace(" ","%",urldecode($nameQuery));
		if ($groupid == 1) //Just get ALL videos for admins, regardless of permissions.
		{
			$this->db->select("`videos`.`id`,`videos`.`name`,`videos`.`description`,`videos`.`author`,`users`.`realname`,`users`.`username`,`videos`.`timeadded`,`videos`.`viewcount`,`videos`.`identifier`,`videos`.`vanity_url`,`videos`.`thumbnail_time`,`videos`.`duration`");
			$this->db->distinct();
			$this->db->from('videos');
			$this->db->join('users','videos.author = users.id');
			if ($nameQuery != "")
			{
				$this->db->where('videos.name LIKE','%' . $nameQuery . '%');
			}
			$query = $this->db->get();
		} else {
			//Get permission based items;
			$this->db->select("`videos`.`id`,`videos`.`name`,`videos`.`description`,`videos`.`author`,`users`.`realname`,`users`.`username`,`videos`.`timeadded`,`videos`.`viewcount`,`videos`.`identifier`,`videos`.`vanity_url`,`videos`.`thumbnail_time`,`videos`.`duration`");
			$this->db->distinct();
			$this->db->from('category_permissions');
			$this->db->join('categories','categories.id = category_permissions.catid');
			$this->db->join('tags','tags.tagID = category_permissions.catid');
			$this->db->join('videos','videos.id = tags.vidID');
			$this->db->join('users','videos.author = users.id');
			$this->db->like('categories.path','/' . $tagid . '/');
			$this->db->where('category_permissions.perm > 0');
			$this->db->where('category_permissions.groupid',$groupid);
			if ($nameQuery != "") {
				$this->db->where('videos.name LIKE','%' . $nameQuery . '%');
			}
		
			$perm_query = $this->db->get_compiled_select("",true);
		
			//Get public items
			$this->db->select("`videos`.`id`,`videos`.`name`,`videos`.`description`,`videos`.`author`,`users`.`realname`,`users`.`username`,`videos`.`timeadded`,`videos`.`viewcount`,`videos`.`identifier`,`videos`.`vanity_url`,`videos`.`thumbnail_time`,`videos`.`duration`");
			$this->db->distinct();
			$this->db->from('categories');
			$this->db->join('tags', 'tags.tagID = categories.id');
			$this->db->join('videos', 'videos.id = tags.vidID');
			$this->db->join('users','videos.author = users.id');
			$this->db->where('categories.public', 1); 
			$this->db->like('categories.path','/' . $tagid . '/');
			if ($nameQuery != "") {
				$this->db->where('videos.name LIKE','%' . $nameQuery . '%');
			}
		
			$pub_query = $this->db->get_compiled_select("",true);
			/*		
			SELECT `videos`.`id`,`videos`.`name`,`videos`.`description`,`videos`.`author`,`videos`.`timeadded`,`videos`.`viewcount`,`videos`.`identifier`,`videos`.`vanity_url`,`videos`.`thumbnail_time`,`videos`.`duration` FROM `category_permissions` 
			JOIN `categories` ON `categories`.`id` = `category_permissions`.`catid`
			JOIN `tags` ON `tags`.`tagID` = `category_permissions`.`catid`
			JOIN `videos` ON `videos`.`id` = `tags`.`vidID`
			JOIN `users` ON `videos`.`author` = `users`.`id`
			WHERE `categories`.`path` LIKE '%/0/%' AND `category_permissions`.`perm` > 0 AND `category_permissions`.`groupid` = 5
			*/
			$query = $this->db->query("SELECT DISTINCT * FROM (" . str_replace(")","",str_replace("(","",$pub_query)) . ") as VIDEOS UNION (" . str_replace(")","",str_replace("(","",$perm_query)) . ")");
		
		}
		
		//echo($this->db->last_query());
		$videos = array();
		foreach ($query->result() as $row)
		{
			array_push($videos,$row);
		}
		return $videos;
	}
	
	function get_random_from_user($uid,$isPublic = false,$number = 1)
	{
		$this->db->select('videos.id, videos.author, videos.name, categories.public, videos.identifier');
		$this->db->distinct();
		$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		$this->db->where('videos.author', $uid); 
		if ($isPublic)
			$this->db->where('categories.public', 1); 
		$this->db->order_by("RAND( )"); 
		$this->db->limit($number);
		$query = $this->db->get();
		return ($query->result());
	}
	
	function fetch_video($identifier,$id = "")
	{
		$this->db->select('id, identifier, author, name, description, timeadded, viewcount, thumbnail_time, duration');
		$this->db->from('videos');
		if ($id != "")
			$this->db->like('id',$id);
		else
			$this->db->like('identifier',$identifier);
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result());
	}
	
	function identifier_to_id($identifier)
	{
		$this->db->select('id');
		$this->db->from('videos');
		$this->db->like('identifier',$identifier);
		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result();
		$result = $result[0];
		$id = $result->id;
		return $id;
	}
	
	function fetch_user_video_count($identifier)
	{
		$this->db->from('videos');
		$this->db->where('author',$identifier);
		return ($this->db->count_all_results());
	}
	
	function fetch_videos_by_user($uid)
	{
		$this->db->select('videos.id, videos.author, videos.name, videos.identifier');
		$this->db->distinct();
		$this->db->from('videos');
		$this->db->join('tags', 'tags.vidID = videos.id');
		$this->db->join('categories', 'categories.id = tags.tagID');
		$this->db->where('videos.author', $uid); 
		$this->db->order_by("id","desc"); 
		$query = $this->db->get();
		
		$videos = array();
		foreach ($query->result() as $row)
		{
			array_push($videos,$row);
		}
		//echo $this->db->last_query();
		return $videos;
	}
	
	/**
	 * Generates a random string as a video identifier.
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
	
	/**
	 * Add this video information to the database, then add it to the specified categories.
	 */
	function add_video($identifier,$author,$name,$desc,$categories,$duration)
	{
		//$identifier = $this->_random(12);
		$data = array(
			'name' => $name ,
			'description' => $desc ,
			'author' => $author ,
			'timeadded' => time() ,
			'cat' => 0 ,
			'type' => 0 ,
			'starttime' => 0 ,
			'viewcount' => 0 ,
			'rating' => 0 ,
			'identifier' => $identifier,
			'vanity_url' => $identifier,
			'duration' => $duration
		);
		$this->db->insert('videos', $data); 
		$id = $this->db->insert_id();
		
		//Always add to drop box
		$tagdata = array(
			'vidID' => $id ,
			'tagID' => $this->config->item('dropbox_id')
		);
		$this->db->insert('tags', $tagdata); 
		
		foreach ($categories as $category)
		{
			$tagdata = array(
				'vidID' => $id ,
				'tagID' => $category 
			);
			$this->db->insert('tags', $data); 
		}
		
		return $id;
	}
	
	/**
	 * Add this video information to the database, preparation for upload to S3 storage.
	 */
	function queue_video($author,$name)
	{
		$identifier = $this->_random(12);
		$data = array(
			'identifier' => $identifier ,
			'userid' => $author ,
			'name' => $name 
		);
		$this->db->insert('queued', $data); 
		if ($this->db->_error_message())
			return false;
		$id = $this->db->insert_id();

		return $identifier;
	}
	
	/**
	 * Set the status of this video in the queue.
	 */
	function set_queue_video_status($identifier,$status,$state = 0)
	{
		$data = array(
			'status' => $status,
			'state' => $state
		);
		$this->db->where('identifier', $identifier);
		$this->db->update('queued', $data); 
	}
	
	/**
	 * Get queue item
	 */
	function fetch_queue_item($identifier)
	{
		$this->db->select('id,identifier,state,userid,name,timequeued,status');
		$this->db->from('queued');
		$this->db->like('identifier',$identifier);
		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result();
		$result = $result[0];
		return $result;
	}
	
	function set_video_title($identifier,$title)
	{
		$data = array(
			'name' => $title
		);
		$this->db->where('identifier', $identifier);
		$this->db->update('videos', $data); 
	}
	
	function set_video_description($identifier,$desc)
	{
		$data = array(
			'description' => $desc
		);
		$this->db->where('identifier', $identifier);
		$this->db->update('videos', $data); 
	}
	
	function set_video_timeadded($identifier,$timeadded)
	{
		$data = array(
			'timeadded' => $timeadded
		);
		$this->db->where('identifier', $identifier);
		$this->db->update('videos', $data); 
	}
	
	function set_video_thumb($identifier,$thumb)
	{
		$data = array(
			'thumbnail_time' => $thumb
		);
		$this->db->where('identifier', $identifier);
		$this->db->update('videos', $data); 
		//Need to delete cached thumbnails, too.
		$id = $this->identifier_to_id($identifier);
		foreach ($this->config->item('thumbnail_sizes') as $size)
		{
			if (file_exists($this->config->item('base_path') . "/images/thumbs/" . $size . "/" . $id . ".jpg"))
				unlink($this->config->item('base_path') . "/images/thumbs/" . $size . "/" . $id . ".jpg");
		}
		if (file_exists($this->config->item('base_path') . "/images/thumbs/backgrounds/" . $id . ".jpg"))
			unlink($this->config->item('base_path') . "/images/thumbs/backgrounds/" . $id . ".jpg");
	}
	
	function set_video_tag($identifier,$tagid)
	{
		$vidid = $this->identifier_to_id($identifier);
		
		$data = array(
			'vidID' => $vidid ,
			'tagID' => $tagid 
		);

		$this->db->insert('tags', $data); 
	}
	
	function unset_video_tag($identifier,$tagid)
	{
		$vidid = $this->identifier_to_id($identifier);
		$this->db->where('vidID',$vidid);
		$this->db->where('tagID',$tagid);
		$this->db->delete('tags');
	}
	
	function video_information($file)
	{
		if (!file_exists($file))
			return false;
		// Create class
		$phpffmpeg = new phpffmpeg($file);
		
		$info = array();
		$info['vcodec'] = $phpffmpeg->vcodec; // Get Video codec name, like "mpeg4"
		$info['acodec'] = $phpffmpeg->acodec; // Get Audio codec name, like "ac3"
		$info['bitrate'] = $phpffmpeg->bitrate; // Get video bitrate, like "1698"
		$info['duration'] = $phpffmpeg->duration; // Get duration of movie, digits type in seconds "621"
		$info['width'] = $phpffmpeg->w; // Get width of video frame
		$info['height'] = $phpffmpeg->h; // Get height of video frame


		$info['framerate'] = $phpffmpeg->framerate; // Get video frame rate, like "23.98"
		$info['audiorate'] = $phpffmpeg->audiorate; // Get audio rate, like "44100"
		$info['channels'] = $phpffmpeg->channels; // Get audio channels, like "5.1"
		$info['abitrate'] = $phpffmpeg->audiobitrate; // Get audio bitrate, like "448"
		
		return $info;
	}
	
	/**
	 * Returns GD image resource handle of thumbnail at given time (seconds) for given file.
	 */
	function getThumb($file,$time = 10)
	{
		//if (!file_exists($file))
		//	return false;
			
		$outputFile = $this->config->item('base_path') . "/images/tmp/" . $this->_random(12) . ".jpg";
		
		$cmd = "ffmpeg  -ss " . $time . " -i '" . $file . "' -vcodec mjpeg -vframes 1 -an -f rawvideo " . $outputFile;
		//echo("<br />" . $cmd . "<br />");
		
		exec ($cmd, $output, $retval);
		$imagehandler = imagecreatefromjpeg($outputFile);
		$size = getimagesize($outputFile);
		$return = array($imagehandler, $size);
		return $return;
	}
	
	/**
	 * Returns data from the user-created poster image
	 */
	function getPoster($id)
	{
		$this->db->select('data,imagetime,prefab');
		$this->db->from('posters');
		$this->db->where('video_id', $id); 
		$query = $this->db->get();
		
		$result = $query->result();
		
		if (count($result) > 0)
		{
			$r = $result[0];
			if ($r->prefab == 1)
				return false;
		}
		return $result;
	}
	
	/**
	 * Sets poster data for video
	 * @id - numerical ID for video
	 * @time - Thumbnail time in seconds for video frame
	 * @val - ASCII data to parse for poster generation
	 */
	function setPoster($id,$time,$val)
	{
		if (file_exists($this->config->item('base_path') . "/images/posters/1920x1080/" . $id . ".jpg")) unlink($this->config->item('base_path') . "/images/posters/1920x1080/" . $id . ".jpg");
		if (file_exists($this->config->item('base_path') . "/images/posters/1280x720/" . $id . ".jpg")) unlink($this->config->item('base_path') . "/images/posters/1280x720/" . $id . ".jpg");
		
		$this->db->where('video_id',$id);
		$this->db->delete('posters');
		
		$data = array(
			'video_id' => $id ,
			'data' => $val ,
			'imagetime' => $time,
			'prefab' => 0
		);

		$this->db->insert('posters', $data); 
	}
}

?>
