<?php
	$this->load->view('general/header');
?>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
	$this->load->view('admin/admin_nav');
?>

	<div id="OverviewContainer" style="margin-top:36px;">
		<div id="OverviewVideoContainer" class="double">
			<?php foreach ($maintag as $video) { ?>
				<div id="<?php echo($video->identifier); ?>" class="videoThumbSmall selectable">
					<div class="videoIcon">
						<div class="videoTitle"><?php echo($video->name); ?></div>
						<img src="<?php echo(site_url("video/thumb/" . $video->identifier . "/180x100")); ?>" alt="Video Thumbnail" />
					</div>
				</div>
			<?php } ?>
		</div>
		<div id="OverviewVideoCount">
			<span class="lg"><?php echo($maintag_count); ?></span> videos<br />
			<span class="lg"><?php echo($maintag_count_wk); ?></span> past wk<br />
			<span class="lg"><?php echo($maintag_count_day); ?></span> past day
		</div>
		<div class="label"><a href="<?php echo(site_url("admin/videos/" . $this->config->item('dropbox_id'))); ?>">Dropbox &raquo;</a></div>
	</div>
	<?php foreach($featured_tags as $featured_tag) { ?>
		<div id="OverviewContainer">
			<div id="OverviewVideoContainer">
				<?php foreach ($featured_tag[2] as $video) { ?>
					<div id="<?php echo($video->identifier); ?>" class="videoThumbSmall">
						<div class="videoIcon">
							<div class="videoTitle"><?php echo($video->name); ?></div>
							<img src="<?php echo(site_url("video/thumb/" . $video->identifier . "/180x100")); ?>" alt="Video Thumbnail" />
						</div>
					</div>
				<?php } ?>
			</div>
			<div id="OverviewVideoCount">
				<span class="lg"><?php echo($featured_tag[1]); ?></span> videos<br />
			</div>
			<div class="label"><a href="<?php echo(site_url("admin/videos/" . $featured_tag[0]->id)); ?>"><?php echo($featured_tag[0]->name); ?> &raquo;</a></div>
		</div>
	<?php } ?>


<?php
	$this->load->view('general/footer');
?>
