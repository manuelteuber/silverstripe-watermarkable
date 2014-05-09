<?php

/**
 * Extend images so you can put watermarks on them.
 *
 * @author memdev.
 * http://www.memdev.de
 *
 */
class ImageWatermarkExtension extends DataExtension {
  
  public function addWatermark(Image $watermarkFile, $position = 3, $transparency = 100) {
    
    // original image
    $size = getimagesize($this->getOwner()->getFullPath());
    $image_width = $size[0];
    $image_height = $size[1];
    $image_type = $size[2];
    
    // watermark should not cover more than 25% of original image
    // TODO: make this dynamic
    $watermark_width = ceil($image_width / 2);
    $watermark_height = ceil($image_height / 2);
    if ($watermarkFile->getWidth() > $watermark_width || $watermarkFile->getHeight() > $watermark_height) {
      $watermarkFile = $watermarkFile->SetRatioSize($watermark_width, $watermark_height);
    }
    $watermark_path = $watermarkFile->getFullPath();
    list($watermark_width, $watermark_height, $watermark_type) = getimagesize($watermark_path);
    
    /**
     * numbers represent the positions on the number pad of a keyboard
     */
    switch ($position) {
      case 9:
        $dest_x = $image_width - $watermark_width;
        $dest_y = 0;
        break;
      case 8:
        $dest_x =  ceil(($image_width / 2));
        $dest_x -= ceil(($watermark_width / 2));
        $dest_y = 0;
        break;
      case 7:
        $dest_x = 0;
        $dest_y = 0;
        break;
      case 6:
        $dest_x =  $image_width - $watermark_width;
        $dest_y =  ceil(($image_height / 2));
        $dest_y -= ceil(($watermark_height / 2));
        break;
      case 5:
        $dest_x =  ceil(($image_width / 2));
        $dest_x -= ceil(($watermark_width / 2));
        $dest_y =  ceil(($image_height / 2));
        $dest_y -= ceil(($watermark_height / 2));
        break;
      case 4:
        $dest_x =  0;
        $dest_y =  ceil(($image_height / 2));
        $dest_y -= ceil(($watermark_height / 2));
        break;
      case 3:
      default:
        $dest_x = $image_width - $watermark_width;
        $dest_y = $image_height - $watermark_height;
        break;
      case 2:
        $dest_x =  ceil(($image_width / 2));
        $dest_x -= ceil(($watermark_width / 2));
        $dest_y = $image_height - $watermark_height;
        break;
      case 1:
        $dest_x = 0;
        $dest_y = $image_height - $watermark_height;
        break;
    }
    
    $transparency = ceil($transparency);
    if ($transparency > 100) {
      $transparency = 100;
    } else if ($transparency < 0) {
      $transparency = 0;
    }
    
    $quality = Config::inst()->get('GDBackend', 'default_quality');
    if (empty($quality)) {
      $quality = 100;
    } else if ($quality > 100) {
      $quality = 100;
    } else if ($quality < 0) {
      $quality = 0;
    }
    
    switch ($watermark_type) {
      case 1:
        $watermark = imagecreatefromgif($watermark_path);
        break;
      case 2:
      default:
        $watermark = imagecreatefromjpeg($watermark_path);
        break;
      case 3:
        $watermark = imagecreatefrompng($watermark_path);
        break;
    }
    
    switch ($image_type) {
      case 1:
        $image = imagecreatefromgif($this->getOwner()->getFullPath());
        break;
      case 2:
      default:
        $image = imagecreatefromjpeg($this->getOwner()->getFullPath());
        break;
      case 3:
        $image = imagecreatefrompng($this->getOwner()->getFullPath());
        break;
    }

    $tmp = imagecreatetruecolor($watermark_width, $watermark_height);
    imagecopy($tmp, $image, 0, 0, $dest_x, $dest_y, $watermark_width, $watermark_height);
    imagecopy($tmp, $watermark, 0, 0, 0, 0, $watermark_width, $watermark_height);
    imagecopymerge($image, $tmp, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $transparency);
    
    switch ($image_type) {
      case 1:
        imagegif($image, $this->getOwner()->getFullPath());
        break;
      case 2:
      default:
        imagejpeg($image, $this->getOwner()->getFullPath(), $quality);
        break;
      case 3:
        imagepng($image, $this->getOwner()->getFullPath(), floor((10 % ($quality / 10))));
        break;
    }
    
    imagedestroy($tmp);
    imagedestroy($image);
    imagedestroy($watermark);
  }
  
}
