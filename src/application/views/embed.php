<!DOCTYPE html>
<html>
	<head>
		<style media="screen" type="text/css">
			html, body {
				margin:0px;
				padding:0px;
				height: 100%;
				width: 100%;
				overflow: hidden;
			}
		</style>
		<script type="text/javascript" src="<?php echo base_url(); ?>/js/jwplayer.js"></script>
		
	</head>
	<body style="background-color:#000;">
		<div id="player">
			<h1>Loading . . .</h1>
		</div>
		<script type="text/javascript">
			jwplayer("player").setup({
				flashplayer: "<?php echo base_url(); ?>/flash/player.swf",
				file: "<?php echo($this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>",
				provider: "rtmp",
				streamer: "rtmp://<?php echo($this->config->item('cloudfront_streaming')); ?>/cfx/st/",
				image: "<?php echo(site_url("video/thumb/" . $video_identifier . "/1280x720")); ?>",
				'width': '100%',
				'height': '100%',
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
		</script>
	</body>
</html>