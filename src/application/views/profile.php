<?php
	$this->load->view('general/header');
?>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>

<div id="PlayerTitle">
	<?php echo($user_displayname); ?>
</div>

<div id="ProfileBody">
	<div class="userInfo">
		<?php echo($user_groupname); ?><br />
		<?php echo("Joined " . date("m.d.y",$user_joindate)); ?><br />
		<?php echo($user_videosuploaded . " Videos Uploaded"); ?><br />
		<form method="link" action="<?php echo(site_url('authentication/logout')); ?>">
			<button style="margin-top:12px;">Log Out</button>
		</form>
	</div>
	<div class="userVideos">
		<?php if (count($user_videos) == 0) { ?>
			<h1 style="padding:32px;">You have no uploaded videos</h1>
		<?php } ?>
		<?php foreach ($user_videos as $video) { ?>
			<a href="<?php echo site_url("video/play/" . $video->identifier); ?>">
				<div class="videoThumbLarge">
					<div class="videoIcon">
						<div class="videoTitle"><?php echo $video->name; ?></div>
						<img src="<?php echo(site_url("video/thumb/" . $video->identifier . "/214x120" )); ?>" alt="Video Thumbnail" />
					</div>
				</div>
			</a>
		<?php } ?>
	</div>
</div>


<?php
	$this->load->view('general/footer');
?>
