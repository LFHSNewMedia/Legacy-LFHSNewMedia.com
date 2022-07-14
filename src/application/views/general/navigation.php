<div id="NavBar">
	<ul>
		<li><a href="<?php echo site_url(); ?>" <?php if (isset($selected_page) && $selected_page == "landing") echo('class="selected"'); ?>>Home</a></li>
		<li><a href="<?php echo site_url("video"); ?>" <?php if (isset($selected_page) && $selected_page == "videos") echo('class="selected"'); ?>>Videos</a></li>
		<li><a href="<?php echo site_url("upload"); ?>" <?php if (isset($selected_page) && $selected_page == "upload") echo('class="selected"'); ?>>Upload</a></li>
		<?php if ($is_admin == true) { ?>
			<li><a href="<?php echo site_url("admin"); ?>" <?php if (isset($selected_page) && $selected_page == "admin") echo('class="selected"'); ?>>Admin</a></li>
		<?php } ?>
		
		<?php if (isset($user_realname)) { ?>
			<li><a href="<?php echo site_url("profile/me"); ?>" class="auth <?php if (isset($selected_page) && $selected_page == "profile") echo('selected'); ?>"><?php echo($user_realname); ?></a></li>
		<?php } else { ?>
			<li><a href="javascript:;" onClick="showLogin();" class="auth">Sign In</a></li>
		<?php } ?>
		<li><a id="SearchButton" href="javascript:;" class="auth"><img src="<?php echo(base_url("img/icon_search.png")); ?>" alt="Search"></a></li>
	</ul>
</div>

<script type="text/javascript">
	var openingRightMenu = 0;
	function openSearchMenu()
	{
		if (openingRightMenu == 1) return;
		if ($('.rightFlyOutMenu').length <= 0) {
			openingRightMenu = 1;
			$.ajax({
				url:'<?php echo(site_url("video/ajax_search_menu"));?>',
				success: function(data) 
				{
					$('body').append(data);
					openingRightMenu = 0;
				},
				error:function (xhr, ajaxOptions, thrownError) {
					showError("Ajax Processing Error","<p>An error occured while processing your request.</p><p>"+xhr.status+" : "+thrownError+"</p>");
				}
			});
		}
	}
	$("#SearchButton").click(openSearchMenu);
	$(function() {
		// action on key down
		$(document).keydown(function(e) {
			if (checkFocus()) return;
			if(e.which == 16) {
				isShift = true; 
			}
			if(e.which == 83 && isShift) { 
				openSearchMenu();
			} 
		});
	});
</script>
