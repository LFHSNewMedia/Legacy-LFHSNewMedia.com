<?php
	$this->load->view('general/header');
?>

<script type="text/javascript" src="<?php echo base_url("/js/html5upload.js"); ?>"></script>

<?php
	$this->load->view('general/begin_body');
	$this->load->view('general/navigation');
?>

<div id="PlayerTitle">
	Video Upload
</div>

<div id="UploadFormBody">
	<h1 id="VideoUploadHeader">Select the video file you wish to upload.</h1>
	<form id="UploadForm" enctype="multipart/form-data" method="post" action="<?php echo(site_url("upload/process")); ?>">
		<p style="margin-top:6px;">Choose a file to upload, or if using Chrome, drag your file into the box below.</p>
		<input type="file" name="videoFile" id="videoFile" onchange="fileSelected();"/>
		<div id="fileName"></div>
		<div id="fileSize"></div>
		<div id="fileType"></div>
		<input type="button" id="UploadButton" onclick="uploadFile();" value="Upload" />
		
	</form>
	<div id="UploadProgress">
		<div id="ProgressNumber"></div>
		<div id="UploadProgressBar"></div>		
	</div>
</div>


<?php
	$this->load->view('general/footer');
?>
