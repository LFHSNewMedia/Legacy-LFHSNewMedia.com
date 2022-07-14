<?php
	$this->load->view('general/header');
?>

<link rel="stylesheet" href="<?php echo base_url("/css/supersized.css"); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url("/theme/supersized.shutter.css"); ?>" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url("/js/jquery.easing.min.js"); ?>"></script>

<script type="text/javascript" src="<?php echo base_url("/js/supersized.3.2.4.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("/theme/supersized.shutter.min.js"); ?>"></script>

<script type="text/javascript">
	
	jQuery(function($){
		
		$.supersized({
		
			// Functionality
			slide_interval          :   <?php echo($slide_interval); ?>,		// Length between transitions
			transition              :   1, 			// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
			transition_speed		:	<?php echo($transition_speed); ?>,		// Speed of transition
													   
			// Components							
			slide_links				:	'blank',	// Individual links for each slide (Options: false, 'number', 'name', 'blank')
			slides 					:  	[			// Slideshow Images
				<?php
					$count = 0;
					foreach ($slides as $slide)
					{
						if ($count != 0)
							echo(",\n");
						echo("{image : '" . $slide['image'] . "', title : '" . htmlentities($slide['title'],ENT_QUOTES) . "', thumb : '" . $slide['thumb'] . "', url : '" . $slide['url'] . "'}");
						$count++;
					}
				?>
										]
		});
    });
</script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>

<div id="LandingLogo">
	<img src="<?php echo base_url(); ?>/img/landing_logo_128.png" alt="Lake Forest High School New Media" />
</div>


<?php
	$this->load->view('general/footer');
?>
