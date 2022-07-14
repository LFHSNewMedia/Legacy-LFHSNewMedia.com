<div id="DialogOverlay" class="embed">
	<div id="DialogContainer" class="embed">
		<div id="EmbedCodeEditor">
			<a href="javascript:;" id="CloseEmbedCodeEditor">X</a>
			<div id="EmbedCodeOptions">
				<h1>Video Embed Code</h1>
				<input id="embedWidth" placeholder="width" value="960" style="width:80px;"/>&nbsp;x&nbsp;<input id="embedHeight" placeholder="height" value="540" style="width:80px;"/>
				<br />
				<button style="width:60%;" id="CloseEmbed">Close</button>
			</div>
			<div id="VideoPoster">
				<textarea id="embedcodebox" style="width:97%;height:240px;"></textarea>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#DialogOverlay.embed').fadeIn(200,function() {
		$('#DialogContainer.embed').fadeIn(300, function() {
			$("#embedcodebox").focus();
			$("#embedcodebox").select();
		});
	});

	$("#embedcodebox").click(function() { $(this).select(); } );

	$("#CloseEmbedCodeEditor").click(closeEmbedCodeEditor);
	$("#CloseEmbed").click(closeEmbedCodeEditor);
	
	function closeEmbedCodeEditor()
	{
		$('#DialogOverlay.embed').fadeOut(300,function() {
			$('#DialogOverlay.embed').remove();
		});
	}
	
	var width = 960;
	var height = 540;
	
	function updateCode()
	{
		width = $("#embedWidth").val();
		height = $("#embedHeight").val();
		//$('#embedcodebox').html("&lt;div id=&quot;player<?php echo($video_identifier); ?>&quot;&gt;Loading...&lt;/div&gt;&lt;script type=&quot;text/javascript&quot; src=&quot;<?php echo base_url(); ?>/js/jwplayer.js&quot;&gt;&lt;/script&gt;&lt;script type=&quot;text/javascript&quot;&gt;jwplayer(&quot;player<?php echo($video_identifier); ?>&quot;).setup({flashplayer: &quot;<?php echo base_url("/flash/player.swf"); ?>&quot;,file: &quot;<?php echo($this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>&quot;,provider: &quot;rtmp&quot;,streamer: &quot;rtmp://<?php echo($this->config->item('cloudfront_streaming')); ?>/cfx/st/&quot;,image: &quot;<?php echo(site_url("video/thumb/" . $video_identifier . "/1280x720")); ?>&quot;,'width': '"+width+"','height': '"+height+"','skin': '<?php echo base_url(); ?>/flash/skins/modieus.zip','modes': [{type: 'flash', src: '<?php echo base_url(); ?>/flash/player.swf'},{type: 'html5',config: {'file': 'http://<?php echo($this->config->item('cloudfront_download') . '/' . $this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>','provider': 'video'}},{type: 'download',config: {'file': 'http://<?php echo($this->config->item('cloudfront_download') . '/' . $this->config->item('s3_video_keyprefix') . $video_identifier . '.mp4'); ?>','provider': 'video'}}]});&lt;/script&gt;");
		$('#embedcodebox').html("&lt;iframe width=&quot;"+width+"&quot; height=&quot;"+height+"&quot; src=&quot;<?php echo site_url("/video/embed/" . $video_identifier); ?>&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;/iframe&gt;");
	}
	
	$(function() {
		updateCode();
		
		$("#EmbedCodeOptions input").observe_field(0.6, function( ) {
			updateCode();
		});
	});
	Cufon.refresh();
</script>

