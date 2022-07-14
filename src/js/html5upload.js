/**
 * Javascript functions for an HTML5 file upload
 * Many thanks to the FANTASTIC tutorial over at
 * http://www.matlus.com/html5-file-upload-with-progress/
 */
 
function fileSelected() {
	var file = document.getElementById('videoFile').files[0];
	if (file) {
		var fileSize = 0;
		if (file.size > 1024 * 1024)
			fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
		else
			fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

		document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
		document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
		document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
		
		//Time to check for user error
		var error = "";
		if (file.size < (1024*1024*8))
			error += "- The size of the video is abnormally small. Ensure that this is a proper video file.<br />";
		if (file.size > (1024*1024*1024*2))
			error += "- The size of the video is abnormally large. Ensure that this is a properly compressed video file.<br />";
		if (file.type.substring(0,5) != "video")
			error += "- This does not appear to be a properly compressed H264 video file.<br />";
			
		if (error != "")
		{
			showMsg("Upload Warning","<p>The file you have chosen appears to be abnormal and/or incorrect. Please ensure that you have selected the proper file before initiating an upload.</p><p>"+error+"</p>");
			/*
			$( '<div style="font-size:12px;" title="Upload Warning">The file you have chosen appears to be abnormal and/or incorrect. Please ensure that you have selected the proper file before initiating an upload.<br /><br /><ul>'+error+'</ul></div>' ).dialog({
				modal: true,
				width: 480,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				}
			});
			*/
		
		}
	}
}

function uploadFile() {
	var fd = new FormData(document.getElementById("UploadForm"));
	//fd.append("videoFile", document.getElementById('videoFile').files[0]);
	var xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", uploadProgress, false);
	xhr.addEventListener("load", uploadComplete, false);
	xhr.addEventListener("error", uploadFailed, false);
	xhr.addEventListener("abort", uploadCanceled, false);
	xhr.open("POST", "upload/process");
	xhr.send(fd);
	$(function() {
		$( "#UploadProgressBar" ).progressbar({
			value: 0
		});
		$( "#UploadProgress" ).show();
		$('#UploadButton').attr('disabled', 'disabled');
		$('#UploadForm').fadeOut();
		$('#UploadFormBody h1').html("Uploading "+document.getElementById('videoFile').files[0].name+"...");
		Cufon.refresh();
	});
}

function uploadProgress(evt) {
	if (evt.lengthComputable) {
		var percentComplete = Math.round(evt.loaded * 100 / evt.total);
		$( "#UploadProgressBar" ).progressbar( "value" , percentComplete );
		document.getElementById('ProgressNumber').innerHTML = percentComplete.toString() + '%';
	}
	else {
		document.getElementById('ProgressNumber').innerHTML = 'unable to compute';
	}
}

function uploadComplete(evt) {
	/* This event is raised when the server send back a response */
	if (evt.target.status != 200) //Check to see that the server response is sane ...
	{
		showError("Server Upload Error","<p>The server gave un enexpected response when attempting to upload. Please contact an adminstrator.</p><p>HTTP Status Code: "+evt.target.status+"</p>","/upload");
		return;
	}
	var jsondata=eval("("+evt.target.responseText+")") //retrieve result as an JavaScript object
	if (jsondata.success)
	{
		showSuccess("Upload Successful","<p>Your video has been queued for processing, it will be available for viewing shortly.</p><p>You can view video processing progress in your user profile.</p>","/profile/me");
	} else {
		showError("Upload Error","<p>Errors were encountered while uploading your video. Please contact an administrator for further assistance.</p><p>"+jsondata.msg+"</p>","/upload");
	}
}

function uploadFailed(evt) {
	alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
	alert("The upload has been canceled by the user or the browser dropped the connection.");
}

/* Some magic to smooth the progress bar */
$.ui.progressbar.prototype._refreshValue = function() {
	var value = this.value();
	this.valueDiv
	.toggleClass( "ui-corner-right", value === this.max )
	.stop().animate( { width: ( value + "%" ) }, 2000 );
	this.element.attr( "aria-valuenow", value );
};
