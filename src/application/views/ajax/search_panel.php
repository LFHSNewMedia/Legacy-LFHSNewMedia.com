<!-- BEGIN OPTIONS MENU -->
<div id="SearchPanel" class="flyOutMenu rightFlyOutMenu">
	<a class="close" href="javascript:;">X</a>
	<div class="innerFlyOutMenu">
		<div style="width:100%;">
			<h1 style="width:256px;float:left;">Search</h1>
			<img id="SearchLoadingIndicator" style="float:right;padding-top:3px;display:none;" src="<?php echo(base_url("/img/dark-loader.gif")); ?>" alt="Searching..." />
			<br style="clear:both;" />
		</div>
		<form id="SearchForm">
			<input name="title" placeholder="Search Query" />
		</form>
		<div id="SearchResults">
		
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#SearchPanel').animate({right:0},650,'easeOutQuart');
	
	function closeSearch()
	{
		$('#SearchPanel').animate({right:-410},400,'easeOutQuart',function() {$('#SearchPanel').remove()});
	}
	
	$('.rightFlyOutMenu a.close').click(closeSearch);
	
	$("#SearchForm").submit(function(event) {
		event.preventDefault(); 
	});
	
	$(function() {
		// Executes a callback detecting changes with a frequency of 1 second
		$("#SearchForm input").observe_field(0.6, function( ) {
			if ($("#SearchForm input").val().length <= 0) return;
			$('#SearchLoadingIndicator').show();
			$.post( "<?php echo site_url("video/ajax_search_results"); ?>/"+encodeURI($("#SearchForm input").val()),
				function( data ) {
					$('#SearchLoadingIndicator').hide();
					$("#SearchResults").html("");
					$.each(data.results, function(i, item) {
						$("#SearchResults").append('<a href="<?php echo site_url("video/play"); ?>/'+item.identifier+'"><div class="videoThumbSmall"><div class="videoIcon"><div class="videoTitle">'+item.title+'</div><img src="<?php echo(site_url("video/thumb/")); ?>/'+item.identifier+'/180x100" alt="Video Thumbnail" /></div></div></a>');
					});
					Cufon.refresh();
				}, 'json'
			);
		});
	});
	
	$("#SearchForm input").focus();
	
	Cufon.refresh();
</script>
<!-- END OPTIONS MENU -->
