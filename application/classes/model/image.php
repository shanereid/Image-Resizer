<?php defined('SYSPATH') or die('No direct script access.');

class Model_Image extends Model {

	public function __construct() {
		$this->db = Database::instance();
	}
	
	public function createThumbnail($originalName,$thumbName,$width,$height, $scale = false) {
	    $system=explode('.',$originalName);
	    $extension = strtolower($system[count($system)-1]);
	    
	    if (preg_match('/jpg|jpeg/',$extension)) {
	        $src_img=imagecreatefromjpeg($originalName);
	    }
	    if (preg_match('/png/',$system[1])) {
	        $src_img=imagecreatefrompng($originalName);
	    }
	    if (preg_match('/gif/',$system[1])) {
	        $src_img=imagecreatefromgif($originalName);
	    }
	    
	    if ($extension == "jpg" || $extension == "jpeg"){
	    	//fix photos taken on cameras that have incorrect
	    	//dimensions
	    	$exif = @exif_read_data($originalName);
	    	//get the orientation
	    	if(isset($exif['Orientation'])) {
		    	$ort = $exif['Orientation'];
		    	//determine what oreientation the image was taken at
		    	switch($ort)
		        {
		            case 2: // horizontal flip
		                $this->ImageFlip($src_img);
		            	break;
		            case 3: // 180 rotate left
		                $src_img = imagerotate($src_img, 180, -1);
		            	break;
		            case 4: // vertical flip
		                $this->ImageFlip($src_img);
		           		break;
		            case 5: // vertical flip + 90 rotate right
		                $this->ImageFlip($src_img);
		                $src_img = imagerotate($src_img, -90, -1);
		            	break;
		            case 6: // 90 rotate right
		                $src_img = imagerotate($src_img, -90, -1);
		            	break;
		            case 7: // horizontal flip + 90 rotate right
		                $this->ImageFlip($src_img);
		                $src_img = imagerotate($src_img, -90, -1);
		            	break;
		            case 8: // 90 rotate left
		                $src_img = imagerotate($src_img, 90, -1);
		            	break;
		        }
	        }
	    }
	    $old_x=imageSX($src_img);
	    $old_y=imageSY($src_img);
	    
	    $orig_w = $old_x;
	    $orig_h = $old_y;
	    
	    if(($old_x < $width || $old_y < $height) && !$scale)
	        return false;
	    
	    if(($old_x / $old_y) < ($width / $height)) {
	        // Use Width
	        if($scale) {
	            $width=$height*($old_x/$old_y);
	        } else {
	            $orig_w=$old_x;
	            $orig_h=$orig_w*($height/$width);
	        }
	    } else if(($old_x / $old_y) > ($width / $height)) {
	        // Use Height
	        if($scale) {
	            $height=$width*($old_y/$old_x);
	        } else {
    	        $orig_h=$old_y;
    	        $orig_w=$orig_h*($width/$height);
	        }
	    } else {
	        // Perfectly Scales
	        $orig_w=$width;
	        $orig_h=$height;
	    }
	    
	    if($scale) {
	        $srcX = 0;
	        $srcY = 0;
	    } else {
	        $srcX = floor(($old_x - $orig_w) / 2);
	        $srcY = floor(($old_y - $orig_h) / 2);
	    }
	    
	    $dst_img=ImageCreateTrueColor($width,$height);
	    imagecopyresampled($dst_img,$src_img,0,0,$srcX,$srcY,$width,$height,$orig_w,$orig_h);
	    
	    
	    if (preg_match("/png/",$extension)) {
	        imagepng($dst_img,$thumbName);
	    } else if(preg_match("/jpg|jpeg/",$extension)) {
	        imagejpeg($dst_img,$thumbName);
	    } else {
	        imagegif($dst_img, $thumbName);
	    }
	    imagedestroy($dst_img);
	    imagedestroy($src_img);
	    
	    return array(ceil($width), ceil($height));
	}
	
	private function ImageFlip(&$image, $x = 0, $y = 0, $width = null, $height = null)
	{
	    if ($width  < 1) $width  = imagesx($image);
	    if ($height < 1) $height = imagesy($image);
	    // Truecolor provides better results, if possible.
	    if (function_exists('imageistruecolor') && imageistruecolor($image))
	    {
	        $tmp = imagecreatetruecolor(1, $height);
	    }
	    else
	    {
	        $tmp = imagecreate(1, $height);
	    }
	    $x2 = $x + $width - 1;
	    for ($i = (int)floor(($width - 1) / 2); $i >= 0; $i--)
	    {
	        // Backup right stripe.
	        imagecopy($tmp, $image, 0, 0, $x2 - $i, $y, 1, $height);
	        // Copy left stripe to the right.
	        imagecopy($image, $image, $x2 - $i, $y, $x + $i, $y, 1, $height);
	        // Copy backuped right stripe to the left.
	        imagecopy($image, $tmp, $x + $i,  $y, 0, 0, 1, $height);
	    }
	    imagedestroy($tmp);
	    return true;
	}
	
	public function Zip($source, $destination)
	{
	    if (!extension_loaded('zip') || !file_exists($source)) {
	        return false;
	    }
	
	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::OVERWRITE)) {
	        return false;
	    }
	
	    $source = str_replace('\\', '/', realpath($source));
	
	    if (is_dir($source) === true)
	    {
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	
	        foreach ($files as $file)
	        {
	            $file = str_replace('\\', '/', realpath($file));
	
	            if (is_dir($file) === true)
	            {
	                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	            }
	            else if (is_file($file) === true)
	            {
	                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	            }
	        }
	    }
	    else if (is_file($source) === true)
	    {
	        $zip->addFromString(basename($source), file_get_contents($source));
	    }
	
	    return $zip->close();
	}
}