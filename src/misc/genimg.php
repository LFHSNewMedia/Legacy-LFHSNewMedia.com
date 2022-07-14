<?php
	
	header('content-type:image/jpeg');
			$image = imagecreatefromjpeg("input.jpg");
		//3rd parameter accepts the brightness level.
		$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
	
		imagefilter($image, IMG_FILTER_BRIGHTNESS, -130);
		//imageconvolution($image, $gaussian, 16, 0);
		for ($i=0;$i<20;$i++)
			imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
		imagejpeg($image,"./img.jpg", 85);
		imagedestroy($image);

?>
