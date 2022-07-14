<?php
	//$file = str_replace($_GET['file'],"/","");
	$base = "../../";
	if (!isset($_GET['id']))
		die("");
	
	header('content-type:image/jpeg');
	
	if (!file_exists($base . "images/thumbs/backgrounds/" . str_replace("/","",$_GET['id']) . ".jpg"))
	{
		$filename = $base . "images/thumbs/poster/" . str_replace("/","",$_GET['id']) . ".jpg";
		if (!file_exists($filename))
			$filename = $base . "images/thumbs/original/" . str_replace("/","",$_GET['id']) . ".png";
		
	
		if (substr($filename,-4) == ".png")
			$image = imagecreatefromjpeg($filename);
		else
			$image = imagecreatefromjpeg($filename);
		//3rd parameter accepts the brightness level.
		$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
	
		imagefilter($image, IMG_FILTER_BRIGHTNESS, -130);
		//imageconvolution($image, $gaussian, 16, 0);
		for ($i=0;$i<20;$i++)
			imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
		imagejpeg($image, $base . "images/thumbs/backgrounds/" . str_replace("/","",$_GET['id']) . ".jpg", 85);
		imagedestroy($image);
	}
	
	@readfile($base . "images/thumbs/backgrounds/" . str_replace("/","",$_GET['id']) . ".jpg");
?>
