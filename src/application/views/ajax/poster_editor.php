<script type="text/javascript" src="<?php echo(base_url('js/jquery.miniColors.min.js')); ?>" ></script>
<link type="text/css" rel="stylesheet" href="<?php echo(base_url('css/jquery.miniColors.css')); ?>" />

<div id="DialogOverlay" class="poster">
	<div id="DialogContainer" class="poster">
		<div id="VideoPosterEditor">
			<a href="javascript:;" id="ClosePosterEditor">X</a>
			<div id="VideoPosterOptions">
				<h1>Edit Video Poster</h1>
				<div id="VideoPosterItems">
					<?php
					if (count($poster) > 0)
					{
						$poster = $poster[0];
						$text_samples = explode("@@",$poster->data);
						foreach ($text_samples as $text_sample)
						{
							echo('<div class="posterTextItem">');
							$text_data = explode("||",$text_sample);
							$text_x = $text_data[0];
							$text_y = $text_data[1];
							$text_size = $text_data[2];
							echo('<input name="text[]" placeholder="Text" value="' . $text_data[3] . '" class="posterInputText" /><br />');
							echo('<input name="x[]" placeholder="X" value="' . $text_data[0] . '" class="posterInputPosition" />%&nbsp;,&nbsp;');
							echo('<input name="y[]" placeholder="Y" value="' . $text_data[1] . '" class="posterInputPosition" />%&nbsp;&nbsp;&nbsp;');
							echo('<input name="size[]" placeholder="Size" value="' . $text_data[2] . '" class="posterInputSize" />&nbsp;pt&nbsp;&nbsp;');
							echo('<input type="hidden" name="color[]" value="' . $text_data[4] . '" class="color-picker" />&nbsp;&nbsp;&nbsp;');
							echo('<a class="close" href="javascript:;">X</a>');
						
							$text_color = $text_data[4];
							$text_color = sscanf($text_color, '#%2x%2x%2x');
							echo('</div>');
						}
					}
					?>
					<button id="AddTextButton" type="button">Add Text Item</button>
				</div>
				<button id="UpdatePosterPreview">Update Preview</button>&nbsp;<button id="SubmitPoster" style="font-weight:bold;">Save Changes</button>
			</div>
			<div id="VideoPoster">
				<img id="PosterThumbnail" src="" />
				<input type="hidden" name="time" id="poster-time" style="border:0; color:#f6931f; font-weight:bold;" value="<?php echo($thumbnail_time); ?>" />
				<div id="poster-time-slider"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	updatePosterPreview();
	$('#DialogOverlay.poster').fadeIn(200,function() {
		$('#DialogContainer.poster').fadeIn(300);
	});
	
	$(".color-picker").miniColors({
		letterCase: 'uppercase',
	});
	
	$("a.close").click(function() {
		$(this).parent(".posterTextItem").slideUp('fast', function() {
			$(this).remove();
		});
	});
	
	$("#UpdatePosterPreview").click(function() {
		updatePosterPreview();
	});
	
	$("#SubmitPoster").click(function() {
		savePosterChanges();
	});
	
	$("#ClosePosterEditor").click(closePosterEditor);
	
	$("#AddTextButton").click(function() {
		$("#AddTextButton").before('<div class="posterTextItem" style="display:none;" ><input name="text[]" placeholder="Text" class="posterInputText" /><br /><input name="x[]" placeholder="X" class="posterInputPosition" />%&nbsp;,&nbsp;<input name="y[]" placeholder="Y" class="posterInputPosition" />%&nbsp;&nbsp;&nbsp;<input name="size[]" placeholder="Size" class="posterInputSize" />&nbsp;pt&nbsp;&nbsp;<input type="hidden" name="color[]" class="color-picker" />&nbsp;&nbsp;&nbsp;<a class="close" href="javascript:;">X</a></div>');
		$(".posterTextItem").last().slideDown('fast');
		$(".color-picker").miniColors({
			letterCase: 'uppercase',
		});
		$("a.close").click(function() {
			$(this).parent(".posterTextItem").slideUp('slow', function() {
				$(this).remove();
			});
		});
	});
	
	function validateInputs()
	{
		var good = true;
		$("#VideoPosterOptions input").each(function (i) {
			$(this).attr('style','');
			if ($(this).val() == "")
			{
				good = false;
				$(this).animate({ borderTopColor: "#c30000",borderRightColor: "#c30000",borderBottomColor: "#c30000",borderLeftColor: "#c30000" }, 'fast');
			}
		});
		return good;
	}
	
	var posterText;
	function updatePosterPreview()
	{
		if (!validateInputs()) return;
		$("#PosterThumbnail").attr("src","<?php echo(base_url("/img/dummy.gif")); ?>");
		posterText = "";
		$("div.posterTextItem").each(function (i) {
			if (posterText.length > 0)
				posterText = posterText + "%40%40";
			posterText = posterText + escape($(this).children('input[name="x[]"]').val() + "||" + $(this).children('input[name="y[]"]').val() + "||" + $(this).children('input[name="size[]"]').val() + "||" + $(this).children('input[name="text[]"]').val() + "||" + $(this).children('input[name="color[]"]').val());
		});
		//alert(posterText);
		setTimeout(function () {
			$("#PosterThumbnail").attr("src","<?php echo(site_url("video/poster/" . $video_identifier . "/853x480/true")); ?>/"+$("#poster-time").val()+"/"+posterText);
		},200);
	}
	
	function savePosterChanges()
	{
		if (!validateInputs()) return;
		$("input").prop('disabled', true);
		updatePosterPreview();
		$.post('<?php echo(site_url("admin/ajax_editposter_submit/" . $video_identifier)); ?>/'+$("#poster-time").val()+'/'+posterText, 
			function(data) {
				$("input").prop('disabled', false);
				if (!data.success)
				{
					showError("Error Saving Video Poster","<p>There was an error that may have prevented your options from being applied.</p><p style=\"font-weight:bold;\">"+data.error+"</p>");
				} else {
					closePosterEditor();
				}
			}, "json"
		);
	}
	
	function closePosterEditor()
	{
		$('#DialogOverlay.poster').fadeOut(300,function() {
			$('#DialogOverlay.poster').remove();
		});
	}
	
	$( "#poster-time-slider" ).slider({
		range: "min",
		value: <?php echo($thumbnail_time); ?>,
		min: 1,
		max: <?php if (isset($duration)) echo($duration); else echo('30'); ?>,
		slide: function( event, ui ) {
			$( "#poster-time" ).val( ui.value );
			$("#PosterThumbnail").attr("src","<?php echo(base_url("/img/dummy.gif")); ?>");
		},
		change: function(event, ui) {
			updatePosterPreview();
		}
	});
	$( "#poster-time" ).val( $( "#poster-time-slider" ).slider( "value" ) );
	
	Cufon.refresh();
</script>

