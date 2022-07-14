<!-- BEGIN OPTIONS MENU -->
<div id="OptionsMenu" class="flyOutMenu leftFlyOutMenu">
	<a class="close" href="javascript:;">X</a>
	<div class="innerFlyOutMenu">
		<div style="width:100%;">
			<h1 style="width:256px;float:left;">Video Options</h1>
			<img id="OptionsSavingIndicator" style="float:left;padding-top:3px;display:none;" src="<?php echo(base_url("/img/dark-loader.gif")); ?>" alt="Saving..." />
			<br style="clear:both;" />
		</div>
		<form id="EditVideoForm">
			<div id="ThumbPreview"><img src="" id="Thumbnail" /></div>
			<input type="hidden" name="thumb" id="amount" style="border:0; color:#f6931f; font-weight:bold;" />
			<div id="slider-range-min"></div>
			<button id="posterEditorButton" type="button">Edit Poster Image</button>
			<input name="title" placeholder="Video Title" value="<?php echo(htmlspecialchars($title)); ?>" />
			<textarea name="desc" placeholder="Description"><?php echo(htmlspecialchars($desc)); ?></textarea>
			<ul class="tags">
				<?php 
					foreach ($tags as $tag)
					{
						echo('<li class="tag" id="tag-' . $tag->id . '" tagid="' . $tag->id . '">' . $tag->name . '</li>');
					}
				?>
				<br class="sep"/>
				<li class="add">Add Tag</li>
				<br style="clear:both;" />
			</ul>
			<input type="submit" value="Save &raquo;" />
			<ul id="buttons">
				<li><a href="<?php echo(site_url("video/play/" . $video_identifier)); ?>">Watch</a></li>
				<li><a id="embedCodeButton" href="javascript:;">Embed Code</a></li>
				<li><a id="deleteButton" href="javascript:;">Delete ...</a></li>
			</ul>
		</form>
	</div>
</div>

<script type="text/javascript">
	$('#OptionsMenu').animate({left:0},650,'easeOutQuart');
	setTimeout(function() {
		$("#Thumbnail").attr("src","<?php echo(site_url("video/thumb/" . $video_identifier . "/256x144")); ?>/<?php echo($thumbnail_time); ?>/true");
	}, 1000);
	$( "#slider-range-min" ).slider({
		range: "min",
		value: <?php echo($thumbnail_time); ?>,
		min: 1,
		max: <?php if (isset($duration)) echo($duration); else echo('30'); ?>,
		slide: function( event, ui ) {
			$( "#amount" ).val( ui.value );
			$("#Thumbnail").attr("src","<?php echo(base_url("/img/dummy.gif")); ?>");
		},
		change: function(event, ui) {
			$("#Thumbnail").attr("src","<?php echo(site_url("video/thumb/" . $video_identifier . "/256x144")); ?>/"+ui.value+"/true");
		}
	});
	$( "#amount" ).val( $( "#slider-range-min" ).slider( "value" ) );
	
	function closeOptions()
	{
		$('#OptionsMenu').animate({left:-410},400,'easeOutQuart',function() {$('#OptionsMenu').remove()});
	}
	
	var openingPosterEditor = 0;
	function openPosterEditor()
	{
		if (openingPosterEditor == 1) return;
		closeOptions();
		openingPosterEditor = 1;
		$.ajax({
			url:'<?php echo(site_url("admin/ajax_editposter/" . $video_identifier));?>',
			success: function(data) 
			{
				$('body').append(data);
				openingPosterEditor = 0;
			},
			error:function (xhr, ajaxOptions, thrownError) {
				showError("Ajax Processing Error","<p>An error occured while processing your request.</p><p>"+xhr.status+" : "+thrownError+"</p>");
			}
		});
	}
	
	$('#posterEditorButton').click(openPosterEditor);
	
	var openingEmbedCode = 0;
	function openEmbedCode()
	{
		if (openingEmbedCode == 1) return;
		closeOptions();
		openingEmbedCode = 1;
		$.ajax({
			url:'<?php echo(site_url("admin/ajax_embedcode/" . $video_identifier));?>',
			success: function(data) 
			{
				$('body').append(data);
				openingEmbedCode = 0;
			},
			error:function (xhr, ajaxOptions, thrownError) {
				showError("Ajax Processing Error","<p>An error occured while processing your request.</p><p>"+xhr.status+" : "+thrownError+"</p>");
			}
		});
	}
	
	$('#embedCodeButton').click(openEmbedCode);
	
	function addTag(name,id)
	{
		$("#deltag-"+id).remove();
		if ($("#tag-"+id).length<=0)
		{
			$("ul.tags br.sep").before('<li class="tag" id="tag-'+id+'" tagid="'+id+'">'+name+'<input type="hidden" name="newTag[]" value="'+id+'"></li>');
		}
		$("#tag-"+id).css("color","#fff");
		$("#tag-"+id).animate({color: '#999'},500,function() {$(this).removeAttr('style')});
		
		$('ul.tags li.tag').click(function() {
			removeTag($(this).attr('tagid'));
		});
	}
	
	function removeTag(id)
	{
		$("#tag-"+id).remove();
		$("form").append('<input type="hidden" id="deltag-'+id+'" name="delTag[]" value="'+id+'">');
	}
	
	$('#OptionsMenu a.close').click(closeOptions);
	
	$("#EditVideoForm").submit(function(event) {
		event.preventDefault(); 
		$('#OptionsSavingIndicator').show();
		$.post('<?php echo(site_url("admin/ajax_editvideo_submit/" . $video_identifier)); ?>', $("#EditVideoForm").serialize(),
			function(data) {
				$('#OptionsSavingIndicator').hide();
				if (!data.success)
				{
					showError("Error Saving Video Options","<p>There was an error that may have prevented your options from being applied.</p><p style=\"font-weight:bold;\">"+data.error+"</p>");
				} else {
					closeOptions();
				}
			}, "json"
		);
	});
	
	$('ul.tags li.tag').click(function() {
		removeTag($(this).attr('tagid'));
	});
	
	// Build Tag Menu
	$.contextMenu({
		selector: 'li.add',
		trigger: 'left', 
		items: {
			<?php
				echo($tag_context);
			?>
		}
	});
	
	Cufon.refresh();
</script>
<!-- END OPTIONS MENU -->
