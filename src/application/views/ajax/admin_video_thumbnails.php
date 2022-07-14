<?php foreach ($videos as $video) { ?>
	<li class="ui-widget-content ui-corner-tr">
		<h5 class="ui-widget-header"><?php echo($video->name); ?></h5>
		<img style="cursor:pointer;" id="<?php echo($video->identifier); ?>" src="<?php echo(site_url("video/thumb/" . $video->identifier . "/214x120" )); ?>"  alt="<?php echo($video->name); ?>" width="214" height="120" />
		<a href="#" id="<?php echo($video->identifier); ?>" title="Preview Video" class="ui-icon ui-icon-zoomin">View Larger</a>
		<a href="#" id="<?php echo($video->identifier); ?>" title="Edit Video" class="ui-icon ui-icon-clipboard">Edit Details</a>
		<a href="link/to/trash/script/when/we/have/js/off" title="Delete this video" class="ui-icon ui-icon-trash">Delete</a>
	</li>
<?php } ?>

<script>
	$(function() {
		// let the gallery items be draggable
		$( "li", ".videoListing" ).draggable({
			cancel: "a.ui-icon", // clicking an icon won't initiate dragging
			revert: "invalid", // when not dropped, the item will revert back to its initial position
			helper: "clone",
			cursor: "move"
		});
	
		// resolve the icons behavior with event delegation
		$( ".videoListing > li" ).click(function( event ) {
			var $item = $( this ),
				$target = $( event.target );

			if ( $target.is( "a.ui-icon-trash" ) ) {
				deleteImage( $item );
			} else if ( $target.is( "a.ui-icon-zoomin" ) ) {
				videoPreview( $target );
			} else if ( $target.is( "a.ui-icon-clipboard" ) ) {
				editVideo( $target );
			} else if ( $target.is( "img" ) ) {
				window.location = '<?php echo(site_url("video/play/")); ?>/'+$target.attr('id');
			}

			return false;
		});
	});
</script>
