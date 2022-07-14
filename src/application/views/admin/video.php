<?php
	$this->load->view('general/header');
?>

<link rel="stylesheet" href="<?php echo base_url("/css/supersized.css"); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url("/theme/supersized.shutter.css"); ?>" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url("/js/supersized.3.2.4.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("/theme/supersized.shutter.min.js"); ?>"></script>

<script type="text/javascript" src="<?php echo base_url("/js/jquery.jstree.js"); ?>"></script>

<script type="text/javascript">
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
	});
</script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>

<div id="PlayerTitle">
	Video Administration
</div>

<div id="AdminBody">
	<div class="leftPane">
		<h2>Tags</h2>
		<!-- To be replaced by AJAX call -->
		<div id="TagTree"></div>
	</div>
	<div class="adminVideos">
		<div class="tagProperties"><h2>Tag Properties</h2></div>
		<!-- To be replaced by AJAX call -->
		<div class="videoListing"></div>
	</div>
</div>

<!-- JavaScript neccessary for the tree -->
<script type="text/javascript" class="source below">

	function videoPreview( $item )
	{
		$('<div id="previewDialog" title="Video Preview">Loading ...</div>').dialog({
			modal:true,
			width:'1000',
			height:'620',
			show: "highlight",
			hide: "drop",
			close: function() {
				$(this).dialog('destroy').remove();
			}
		});
		$.ajax({
			url:'<?php echo(site_url("video/preview/"));?>'+'/'+$item.attr('id'),
			success: function(data) 
			{
				$('#previewDialog').html(data);
				//$('#previewDialog').dialog('option', 'height', $('#editdialog').height() + 50);
				//$('#previewDialog').dialog('option', 'width', $('#editdialog').width() + 50);
				$('#previewDialog').dialog('option', 'position', 'center');
				$('#previewDialog').dialog('open');
			}
		});
	
	
	
	}
	
	function editVideo( $item )
	{
		$('<div id="editDialog" title="Video Information">Loading ...</div>').dialog({
			modal:true,
			show: "highlight",
			hide: "drop",
			width:'347',
			height: '625',
			close: function() {
				$(this).dialog('destroy').remove();
			}
		});
		$.ajax({
			url:'<?php echo(site_url("admin/ajax_editvideo/"));?>'+'/'+$item.attr('id'),
			success: function(data) 
			{
				$('#editDialog').html(data);
				$('#editDialog').dialog('option', 'height', $('#editDialog').height());
				$('#editDialog').dialog('option', 'width', $('#editDialog').width());
				$('#editDialog').dialog('option', 'position', 'center');
				$('#editDialog').dialog('open');
			}
		});
	}

	$(function () {

		$("#TagTree")
		.jstree({ 
			// List of active plugins
			"plugins" : [ 
				"json_data","ui","themeroller"
			],

			// I usually configure the plugin that handles the data first
			// This example uses JSON as it is most common
			"json_data" : { 
				// This tree is ajax enabled - as this is most common, and maybe a bit more complex
				// All the options are almost the same as jQuery's AJAX (read the docs)
				"ajax" : {
					// the URL to fetch the data
					"url" : "<?php echo(site_url("admin/ajax_fetch_tags/")); ?>",
					// the `data` function is executed in the instance's scope
					// the parameter is the node being loaded 
					// (may be -1, 0, or undefined when loading the root nodes)
					"data" : function (n) { 
						// the result is fed to the AJAX request `data` option
						return { 
							"operation" : "get_children", 
							"id" : n.attr ? n.attr("id").replace("node_","") : 0 
						}; 
					}
				}
			}
		
		
		})
	
		$("#TagTree").bind("select_node.jstree", function(event, data) {
			$(".videoListing").load('<?php echo(site_url("admin/ajax_fetch_videos/")); ?>/'+data.rslt.obj.attr('id'));
			$(".tagProperties").load('<?php echo(site_url("admin/ajax_tag_properties/")); ?>/'+data.rslt.obj.attr('id'));
		});
	});

</script>


<?php
	$this->load->view('general/footer');
?>
