silverstripe-watermarkable
==========================

Extends your SilverStripe 3 site to add watermarks to your images.

## Basic Usage
Extend your image class from the "WatermarkImage" class and implement the methods `getWatermark`, `getWatermarkPosition` and `getWatermarkTransparency`.
```php
<?php

class MyImage extends WatermarkImage {

  // by overriding this, you can define whether to automatically add the watermark or not
  // (this can also be controlled in templates for every single image)
  protected $addWatermark = true;
  
  /**
   * @return Image
   */
  public function getWatermark() {
    // in this example we assume has an image named "Watermark"
    $siteConfig = SiteConfig::current_site_config();
    if ($siteConfig->Watermark()) {
      return $siteConfig->Watermark();
    }
    return null;
  }
  
  /**
	 * @return int
	 */
  public function getWatermarkPosition() {
    // return the position at which the watermark should appear on the image
    // can be 1 to 9 (representing the positions on your number pad)
    return 3; // bottom right
  }
  
	/**
	 * @return int
	 */
  public function getWatermarkTransparency() {
    // return the transparency of the watermark
    // can be 0 to 100 (0 = fully transparent, 100 = no transparency)
    return 90;
  }
  
}
```

In your DataObject, use class `MyImage` instead of `Image` for your images.
```php
<?php
class MyDataObject extends DataObject {
  
  public static $has_one = array(
    'CoverImage' => 'MyImage'
  );
  
  public static $has_many = array(
    'Images' => 'MyImage'
  );
  
}
```

### Template Usage
In your templates, you can switch on and off the watermark:
```html
<% with $CoverImage %>
  <!-- this image will have a watermark, if $addWatermark (from the first example) is set to true, 
       otherwise the watermark is omitted -->
  <img src="$SetRatioSize(400, 300).URL" />
<% end_with %>

<% loop Images %>
  <!-- example: we do not want the watermark to appear on thumbnails, but we want it on our big images -->
  <a href="$WithWatermark.SetRatioSize(800, 800).URL">
    <img src="$WithoutWatermark.CroppedImage(100, 100).URL" />
  </a>
<% end_loop %>
```

### Add watermark to standard images
You can also add a watermark to an image explicitly in PHP:
```php
// assuming we have a $has_one = array('Image' => 'Image')
$this->Image()->addWatermark($watermarkImageObject, $watermarkPosition, $watermarkTransparency);
```


You are welcome to improve this module and send me your Pull Requests.
