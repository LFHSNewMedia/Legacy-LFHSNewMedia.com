<?php
	$this->load->view('general/header');
?>

<script type="text/javascript" src="<?php echo base_url("/js/html5upload.js"); ?>"></script>

<script type="text/javascript">
	jQuery(function($){
		
		/* attach a submit handler to the form */
		$("#authform").submit(function(event) {
			//Get form data before disabling form
			formdata = $('#authform').serialize();
			/* Clear Errors */
			//$("#authform #error").stop().fadeOut(function() {
				$("#authform #error").html("");
			//});
			/* Submit colors for inputs */
			$('#authform input').attr('disabled', true);
			$('#authform input:text,#authform input:password').css('border-width','1px');
			$('#authform input:text,#authform input:password').animate({ borderTopColor: "#000",borderRightColor: "#000",borderBottomColor: "#000",borderLeftColor: "#000" }, 'slow');
			$('#authform #loader').show();
			/* stop form from submitting normally */
			event.preventDefault(); 

			/* Send the data using post and put the results in a div */
			$.post( "<?php echo site_url("authentication/verify"); ?>", formdata,
				function( data ) {
					$('#authform #loader').hide();
					if (data.error.length > 0)
					{
						$("#authform #error").html(data.error);
						$("#authform #error").stop().fadeIn();
						$('#authform input:text,#authform input:password').animate({ borderTopColor: "#c30000",borderRightColor: "#c30000",borderBottomColor: "#c30000",borderLeftColor: "#c30000",borderWidth: "2px" }, 'fast');
						$('#authform input').attr('disabled', false);
					} else {
						window.location.href = "<?php echo site_url("profile/me"); ?>";
					}
				}, 'json'
			);
		});
	});
</script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>

<div id="PlayerTitle">
	Authentication
</div>

<div id="UploadFormBody">
	<h1 id="VideoUploadHeader">You must log in to continue.</h1>
	<form id="authform" method="post" action="<?php echo(site_url("upload/auth")); ?>">
		<div id="error"></div>
		<input name="username"><br />
		<input name="password" type="password"><br />
		<button type="submit">Log In</button>
		<img id="loader" style="display:none;" src="<?php echo base_url('/img/ajax-loader.gif'); ?>" alt="Loading ...">
		
	</form>
</div>


<?php
	$this->load->view('general/footer');
?>
