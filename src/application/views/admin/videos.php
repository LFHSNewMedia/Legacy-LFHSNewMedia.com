<?php
	$this->load->view('general/header');
?>

<link rel="stylesheet" href="<?php echo base_url("/css/supersized.css"); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url("/theme/supersized.shutter.css"); ?>" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url("/js/supersized.3.2.4.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("/theme/supersized.shutter.min.js"); ?>"></script>

<script type="text/javascript" src="<?php echo base_url("/js/jquery.jstree.js"); ?>"></script>

<script type="text/javascript">
	var offset = 0;
	var done = 0;
	var populating = 0;
	
	jQuery(function($){
		$.supersized({
			// Functionality
			slide_interval : 1000,
			transition : 0,
			transition_speed : 1000,
			// Components							
			slide_links : 'blank',
			slides : [			// Slideshow Images
				<?php
					echo("{image : '" . base_url("/img/background_generic_01.jpg") . "', title : '', thumb : '', url : ''}");
				?>
			]
		});
		
		$(window).scroll(function(){
			if ($(window).scrollTop() == $(document).height() - $(window).height()){
				populateVideos();
			}
		});
		setTimeout(checkScroll,500);
	});
	
	function checkScroll()
	{
		if ( ($(document).height() - $(window).height()) <= 0 && populating == 0)
		{
			populateVideos();
		}
		setTimeout(checkScroll,500);
	}
	
	function populateVideos()
	{
		if (done == 1) return;
		populating = 1;
		$.post('<?php echo(base_url()); ?>/admin/ajax_fetch_videos_by_tag/<?php echo($tag_id); ?>/32/'+offset,
			function( data ) {
				populating = 0;
				if (data.length <= 0)
				{
					done = 1;
					return;
				}
				$.each(data, function(i, item) {
					$("#VideoListing").append('<a href="<?php echo site_url("video/play"); ?>/'+item.identifier+'"><div class="videoThumbSmall"><div class="videoIcon"><div class="videoTitle">'+item.name+'</div><img src="<?php echo(site_url("video/thumb/")); ?>/'+item.identifier+'/180x100" alt="Video Thumbnail" /></div></div></a>');
				});
				Cufon.refresh();
				offset+=32;
			}, 'json'
		);
	}
</script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
	$this->load->view('admin/admin_nav');
?>

	<div id="VideoListing">
	</div>

<?php
	$this->load->view('general/footer');
?>
