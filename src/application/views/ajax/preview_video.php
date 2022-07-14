<script type="text/javascript" src="<?php echo base_url(); ?>/js/jwplayer.js"></script> 
<div id="player">
	<h1>Loading . . .</h1>
</div>
<script type="text/javascript"> 
	jwplayer("player").setup({
		flashplayer: "<?php echo base_url(); ?>/flash/player.swf",
		file: "/videos/<?php echo($video_id); ?>.m4v",
		image: "<?php echo(site_url("video/thumb/" . $video_identifier . "/1280x720")); ?>",
		'width': '960',
		'height': '540',
		'skin': '<?php echo base_url(); ?>/flash/skins/modieus.zip',
		'autoplay': true
	});
</script>
