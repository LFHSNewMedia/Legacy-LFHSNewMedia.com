<!DOCTYPE html>
<html>
<head>
	<title><?php echo($page_title); ?> | Lake Forest High School New Media Template</title>
	<script type="text/javascript" src="<?php echo base_url("/js/jquery-1.7.1.min.js"); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("/js/jquery-ui-1.8.17.custom.min.js"); ?>"></script>
	<link rel="stylesheet" href="<?php echo base_url("/css/dot-luv/jquery-ui-1.8.17.custom.css"); ?>" type="text/css">
	
	<script type="text/javascript" src="<?php echo base_url("/js/jquery.observe_field.js"); ?>"></script>
	
	<!-- Options menu scripts -->
	<link href="<?php echo(base_url('/css/jquery.contextMenu.css')); ?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo(base_url('/js/jquery.ui.position.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo(base_url('/js/jquery.contextMenu.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo(base_url('/js/screen.js')); ?>"></script>
	
	<script type="text/javascript" src="<?php echo base_url("/js/cufon-yui.js"); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("/fonts/aaargh_400.font.js"); ?>"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url("/css/general.css"); ?>"> 
	
	<script type="text/javascript" src="<?php echo base_url("/js/class.js"); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url("/js/stackblur.min.js"); ?>"></script>
	
	<script type="text/javascript" src="<?php echo base_url("/js/newmedia_common.js"); ?>"></script>
	
	<script type="text/javascript">
		Cufon.replace('#NavBar ul li a, .mb-inside h2, #PlayerTitle,.featurecarousel_title,#ProfileBody .userInfo,#UploadFormBody h1,#AdminBody .leftPane h2,#AdminBody .adminVideos h2, h1, div.videoThumbLarge .videoTitle, div.videoThumbSmall .videoTitle,#VideoPosterOptions h1,#OverviewContainer .label,#OverviewVideoCount');
		
		<!-- Set up the page's background image-->
		$(function() {
			var background = new Blur({ el : document.querySelector('body'), path : '<?php echo($background); ?>', radius : 20, fullscreen : true });
		});
	</script>

