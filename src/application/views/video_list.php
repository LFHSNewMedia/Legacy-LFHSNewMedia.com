<?php
	$this->load->view('general/header');
?>

<script type="text/javascript" src="<?php echo base_url("/js/jquery.easing.min.js"); ?>"></script>

<link href="<?php echo base_url("/css/movingboxes.css"); ?>" media="screen" rel="stylesheet">
<!--[if lt IE 9]>
<link href="<?php echo base_url(); ?>/css/movingboxes-ie.css" rel="stylesheet" media="screen" />
<![endif]-->
<script src="<?php echo base_url("/js/jquery.movingboxes.min.js"); ?>"></script>
<script src="<?php echo base_url("/js/jcarousellite_1.0.1.min.js"); ?>"></script>

<script type="text/javascript">
	$(function() {
		
	});
</script>
<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>
<div id="PlayerTitle">
	Featured Videos
</div>
<!-- <div id="GeneralContent"> -->
<div id="FeaturedVideosPane" class="LargeVideoPane">
	<div class="smallColumn">
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
	</div>
	<div class="largeThumb">
		<div class="thumb"></div>
	</div>
	<div class="smallColumn">
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
		<div class="smallThumb">
			<div class="thumb"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	Cufon.replace("div.LargeVideoPane .largeThumb .thumb .videoTitle");
	function fillSmallThumb(element,video)
	{
		var thumb = $('<div id="'+video.identifier+'" class="thumb" style="display:none;"><img src="<?php echo(site_url("video/thumb"));?>/'+video.identifier+'/214x120"><a href="/video/play/'+video.identifier+'"></a></div>');
		thumb.hide();
		element.children(".thumb").fadeOut(700,function() {
			$(this).remove();
			element.append(thumb);
			thumb.fadeIn(700);
		});
		
	}
	function fillLargeThumb(element,video)
	{
		var thumb = $('<div id="'+video.identifier+'" class="thumb" style="display:none;"><img src="<?php echo(site_url("video/thumb"));?>/'+video.identifier+'/640x360"><div class="videoTitle">'+video.title+'</div><a href="/video/play/'+video.identifier+'"></a></div>');
		thumb.hide();
		element.children(".thumb").fadeOut(700,function() {
			$(this).remove();
			element.append(thumb);
			thumb.fadeIn(700);
			Cufon.refresh();
		});
		
	}
	function randomLargeThumb(videoList)
	{
		var rand = Math.floor(Math.random()*(videoList.length));
		while ($("#FeaturedVideosPane #"+videoList[rand].identifier).length>0)
		{
			rand = Math.floor(Math.random()*(videoList.length));
		}
		fillLargeThumb($('#FeaturedVideosPane .largeThumb'),videoList[rand]);
		setTimeout(function(){randomLargeThumb(videoList)},15000);
	}
	function randomSmallThumb(videoList)
	{
		var randSmThumb = Math.floor(Math.random()*6);
		var rand = Math.floor(Math.random()*(videoList.length));
		while ($("#FeaturedVideosPane #"+videoList[rand].identifier).length>0)
		{
			rand = Math.floor(Math.random()*(videoList.length));
		}
		fillSmallThumb($('#FeaturedVideosPane .smallThumb:eq('+randSmThumb+')'),videoList[rand]);
		setTimeout(function(){randomSmallThumb(videoList)},5000);
	}
	
	$(function() {
		var largePaneVideos = jQuery.parseJSON('<?php echo($popular_slider_json); ?>');
		fillLargeThumb($('#FeaturedVideosPane .largeThumb'),largePaneVideos[0]);
		$('#FeaturedVideosPane .smallThumb').each(function (index,Element) {
			setTimeout(function(){fillSmallThumb($(Element),largePaneVideos[index+1]);},Math.random()*1000);
		});
		setTimeout(function(){randomLargeThumb(largePaneVideos)},15000+(Math.random()*5000));
		setTimeout(function(){randomSmallThumb(largePaneVideos)},5000);
	});
</script>

<?php
	$this->load->view('general/footer');
?>
