<?php

/**
 * Extend this class to put watermarks on your images.
 *
 * @author memdev.
 * http://www.memdev.de
 *
 */
class WatermarkImage extends Image {
  
  static $backend = "WatermarkGD";
  protected $addWatermark = true;
  
  public function WithoutWatermark() {
    $this->addWatermark = false;
    return $this;
  }
  
  public function WithWatermark() {
    $this->addWatermark = true;
    return $this;
  }
  
  /**
   * This function must be implemented in a subclass and
   * return an Image object that should be used as watermark.
   *
   * @return Image
   */
  public function getWatermark() {
    return null;
  }
  
	/**
   * Returns the position of the watermark on the final image
   * Possible positions: 1 to 9 (referencing the positions on the numberblock of a keyboard)
   *
   * @return int
   */
  public function getWatermarkPosition() {
    return 3;
  }
  
	/**
   * Returns the transparency value for the watermark
   * Possible value: 0 to 100 (with 0 being fully transparent and 100 no transparency)
   *
   * @return int the transparency value the watermark should have on the final image
   */
  public function getWatermarkTransparency() {
    return 100;
  }
  
  public function cacheFilename($format, $arg1 = null, $arg2 = null) {
		$folder = $this->ParentID ? $this->Parent()->Filename : ASSETS_DIR . "/";
		
		$format = $format.$arg1.$arg2;
		$format .= $this->addWatermark ? ('-wm'.intVal($this->getWatermarkPosition()).intVal($this->getWatermarkTransparency())) : '';
		
		return $folder . "_resampled/$format-" . $this->Name;
	}
  
	public function generateFormattedImage($format, $arg1 = null, $arg2 = null) {
		$cacheFile = $this->cacheFilename($format, $arg1, $arg2);
		
		$backend = Injector::inst()->createWithArgs(self::$backend, array(
			Director::baseFolder()."/" . $this->Filename
		));
		
		if($backend->hasImageResource()) {

			$generateFunc = "generate$format";
			if($this->hasMethod($generateFunc)){
				$backend = $this->$generateFunc($backend, $arg1, $arg2);
				if($backend){
				  $watermark = $this->getWatermark();
				  if ($this->addWatermark && $watermark && $watermark instanceof Image) {;
            $backend->setWatermark($watermark, $this->getWatermarkPosition(), $this->getWatermarkTransparency());
				  }
					$backend->writeTo(Director::baseFolder()."/" . $cacheFile);
				}
	
			} else {
				user_error("Image::generateFormattedImage - Image $format public function not found.",E_USER_WARNING);
			}
		}
	}
  
}