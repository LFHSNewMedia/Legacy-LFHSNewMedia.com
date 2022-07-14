<?php
	$this->load->view('general/header');
?>

<link rel="stylesheet" href="<?php echo base_url(); ?>/css/supersized.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url(); ?>/theme/supersized.shutter.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url(); ?>/css/jquery.ui.stars.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url(); ?>/js/jquery.easing.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>/js/expand.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>/js/jquery.ui.stars.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>/js/jwplayer.js"></script> 

<script type="text/javascript">
	$(function() {
		$("h2.expand").toggler({method: "slideToggle"});    
	});

	Cufon.replace('h2.expand, h2, #VideoInformation .tags ul li,#VideoInformation .byline .author,#VideoInformation .byline', {
		hover:true
	});
</script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>
<div id="PlayerTitle">
	<?php echo(htmlspecialchars($video_title)); ?>
</div>
<div id="mediaplayer">
	<div id="player">
		<h1>Loading . . .</h1>
	</div>
	
	<div id="VideoPost">
		<div id="RatingPane">
			What did you think?<br />
			
		</div>
		<img id="Separator" src="<?php echo base_url(); ?>/img/post_video_separator.png" alt="" />
		<div id="RelatedPane">
			More like this . . .
		</div>
		
		<div id="BottomPane">
			<a id="ReplayButton" href="javascript:;"><img src="<?php echo base_url(); ?>/img/replay_arrow.png" alt="Replay" />Replay</a>
		</div>
	</div>
	<h2 class="expand">Video Information</h2>
	<div id="VideoInformation" class="collapse">
		<div class="byline"><div class="by">by&nbsp;<span class="author"><?php echo($video_author); ?></span></div><div class="time">Uploaded&nbsp;<?php echo(date("m.j.Y",$video_timeadded)); ?></div></div>
		<div class="video_description">
			<p><?php echo(htmlspecialchars($video_description)); ?></p>
			<?php if ($video_canedit) { ?>
				<button id="EditVideoButton">Video Options</button>
			<?php } ?>
		</div>
		<div class="tags">
			<ul>
				<?php foreach ($tag_list as $tag) { ?>
					<li><?php echo($tag[1]); ?></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<div id="DialogOverlay">
	<div id="DialogContainer" class="errorDialog">
		<div id="DialogMessage">
			<h1>Whoops!</h1>
			<p>An error was encountered while saving video options. Please correct any problems and try again.</p>
			<p>Specific error message here.</p>
			<button>Close</button>
		</div>
	</div>
</div>

<script type="text/javascript"> 
	jwplayer("player").setup({
		flashplayer: "<?php echo base_url(); ?>/flash/player.swf",
		file: "<?php echo($this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>",
		provider: "rtmp",
		streamer: "rtmp://<?php echo($this->config->item('cloudfront_streaming')); ?>/cfx/st/",
		image: "<?php echo(site_url("video/thumb/" . $video_identifier . "/1280x720")); ?>",
		'width': '960',
		'height': '540',
		'skin': '<?php echo base_url(); ?>/flash/skins/modieus.zip',
		'modes': [
			{type: 'flash', src: '<?php echo base_url(); ?>/flash/player.swf'},
			{
				type: 'html5',
				config: {
					'file': 'http://<?php echo($this->config->item('cloudfront_download') . '/' . $this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>',
					'provider': 'video'
				}
			},
			{
				type: 'download',
				config: {
					'file': 'http://<?php echo($this->config->item('cloudfront_download') . '/' . $this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>',
					'provider': 'video'
				}
			}
		]
	});
	jwplayer('player').onComplete(
		function(event) {
			showPost();
		}
	);
	function showPost()
	{
		/* $("#player_wrapper").hide(); */
		$("#player_wrapper").fadeOut(300, function() {
			$("#VideoPost").fadeIn(300);
		});
	}
	
	function reshowVideo()
	{
		/* $("#player_wrapper").hide(); */
		$("#VideoPost").fadeOut(300, function() {
			$("#player_wrapper").fadeIn(300, function() {
				jwplayer('player').seek(0);
				jwplayer('player').play(true);
			});
		});
	}
	
	//showPost(); //DEBUGGING
	
	$("#stars-wrapper").stars({
		inputType: "select",
		captionEl: $("#stars-cap"),
		cancelShow: false
	});
	
	$("#ReplayButton").click(function() {
		reshowVideo();
	});
	
	$("#EditVideoButton").click(function() {
		openVideoOptions("<?php echo($video_identifier); ?>");
	});
	$(function() {
		// action on key down
		$(document).keydown(function(e) {
			if (checkFocus()) return;
			if(e.which == 16) {
				isShift = true; 
			}
			if(e.which == 81 && isShift) { 
				openVideoOptions("<?php echo($video_identifier); ?>");
			} 
		});
	});
	
</script> 

<?php
	$this->load->view('general/footer');
?>
