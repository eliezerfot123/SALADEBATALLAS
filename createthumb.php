<?php
/*


*/
//	error_reporting(E_ALL);

if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) header('Content-type: image/jpeg');
if (preg_match("/.gif$/i", $_GET['filename'])) header('Content-type: image/gif');
if (preg_match("/.png$/i", $_GET['filename'])) header('Content-type: image/png');

	// Visualización de la imagen de error si el archivo no se encuentra
	if (!is_file($_GET['filename'])) {
		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG('images/questionmark.jpg');
		ImageJPEG($errorimage,null,90);
	}
	
	// imagen de la pantalla de error si el archivo existe, pero no se puede abrir
	if (substr(decoct(fileperms($_GET['filename'])), -1, strlen(fileperms($_GET['filename']))) < 4 OR substr(decoct(fileperms($_GET['filename'])), -3,1) < 4) {
		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG('images/cannotopen.jpg');
		ImageJPEG($errorimage,null,90);
	}
	
	// Define variables
	$target = "";
	$xoord = 0;
	$yoord = 0;

    if ($_GET['size'] == "") $_GET['size'] = 120; //
       $imgsize = GetImageSize($_GET['filename']);
       $width = $imgsize[0];
       $height = $imgsize[1];
      if ($width > $height) { // If the width is greater than the height it’s a horizontal picture
        $xoord = ceil(($width-$height)/2);
        $width = $height;      // Then we read a square frame that  equals the width
      } else {
        $yoord = ceil(($height-$width)/2);
        $height = $width;
      }

    // Rotate JPG pictures
    if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) {
		if (function_exists('exif_read_data') && function_exists('imagerotate')) {
			$exif = exif_read_data($_GET['filename']);
			$ort = $exif['IFD0']['Orientation'];
			$degrees = 0;
		    switch($ort)
		    {
		        case 6: // 90 rotate right
		            $degrees = 270;
		        break;
		        case 8:    // 90 rotate left
		            $degrees = 90;
		        break;
		    }
			if ($degrees != 0)	$target = imagerotate($target, $degrees, 0);
		}
	}
	
         $target = ImageCreatetruecolor($_GET['size'],$_GET['size']);
         if (preg_match("/.jpg$/i", $_GET['filename'])) $source = ImageCreateFromJPEG($_GET['filename']);
         if (preg_match("/.gif$/i", $_GET['filename'])) $source = ImageCreateFromGIF($_GET['filename']);
         if (preg_match("/.png$/i", $_GET['filename'])) $source = ImageCreateFromPNG($_GET['filename']);
         imagecopyresampled($target,$source,0,0,$xoord,$yoord,$_GET['size'],$_GET['size'],$width,$height);
		 imagedestroy($source);

         if (preg_match("/.jpg$/i", $_GET['filename'])) ImageJPEG($target,null,90);
         if (preg_match("/.gif$/i", $_GET['filename'])) ImageGIF($target,null,90);
         if (preg_match("/.png$/i", $_GET['filename'])) ImageJPEG($target,null,90); // Using ImageJPEG on purpose
         imagedestroy($target);



?>