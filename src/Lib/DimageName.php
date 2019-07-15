<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Marcohern\Dimages\Lib\Dimages\Dimage;
use Marcohern\Dimages\Lib\Dimages\DimageConstants;
use Marcohern\Dimages\Exceptions\DimagesException;

/**
 * Filename components of a Dimage. It follows an specific format,
 * Which we can import and export. The images are associated to a target
 * called entity, and it's identity is the target itself. Think of
 * your profile picture, users are the entity, and you are the identity.
 * Some entities can have more than one picture, so we use an index to 
 * distinguish them.
 * such as <entity>.<identity>.<index>.<profile>.<density>.<ext>
 * example: user.john-doe.3.icon.hdpi.jpg
 */
class DimageName {

  /**
   * Represents the entity of the target.
   * For example, for user profiles, the entity 
   * can be: user-profile.
   */
  public $entity;

  /**
   * The identity of the target. usually a  slug of
   * a propper name. For example, for user profiles
   * the identity can be: tom-cruise or brad-pitt.
   */
  public $identity;

  /**
   * Index of the image. Some items can have more than
   * one image, so we manage them using a numeric index.
   */
  public $index;

  /**
   * Dynamic property, represents the size of the image,
   * which is essentially what the image will be used
   * for. For example, you may want a medium square image
   * to display the user profiles at the top of the profile
   * page. You may also want a small square image of the
   * profile pic as an icon, displayed in comments
   * sections, for instance. So we would have at least 
   * two profiles: user-default and comment-icon.
   * If it's empty, it means this image is the source in it's
   * original size.
   */
  public $profile = '';

  /**
   * Density of the image. It modifies the target size of the
   * resulting image, to adjust for the capacity of the device.
   * examples include: ldpi, mdpi, hdpi, etc.
   */
  public $density = '';

  /**
   * File Extension of the image. such as jpg, jpeg or png.
   */
  public $ext;

  /**
   * Basic function used to format the name or url of the image.
   * The source image format can have any of the following parameters:
   * %entity    users, pets, books...
   * %identity  john-doe, mr-snuggles, harry-potter-and-the-goblet-of-fire
   * %index     Numeric: 1, 2, 3...
   * %pindex    Padded: 001, 002, 003...
   * %profile   large-icon, user-profile, top-bar
   * %density   ldpi, mdpi, hdpi, xhdpi
   * %ext       jpg, jpeg, png
   * 
   * @param $source image format
   */
  protected function replace(string $source) {
    $source = str_replace('%entity', $this->entity, $source);
    $source = str_replace('%identity', $this->identity, $source);
    $source = str_replace('%index', $this->index, $source);
    $source = str_replace('%pindex', DimageFunctions::padIndex($this->index), $source);
    $source = str_replace('%profile', $this->profile, $source);
    $source = str_replace('%density', $this->density, $source);
    $source = str_replace('%ext', $this->ext, $source);
    return $source;
  }

  /**
   * Create a DimageName from a source. The source could be a filename or a url.
   * This method tries to extract the DimageName from a source (haystack) using a
   * regular expression.
   * 
   * @param $needle Regular expression
   * @param $haystack Source to search from.
   */
  protected static function from(string $needle, string $haystack) : DimageName {
    $m = null;
    $r = preg_match($needle, $haystack, $m);
    if (!$r) {
      throw new DimagesException("source invalid: $haystack.", 0xa996a53d53);
    }
    $inf = new DimageName;
    $inf->entity   =   $m['entity'];
    $inf->identity =   $m['identity'];
    $inf->index    = 0+$m['index'];
    $inf->profile  =   $m['profile'];
    $inf->density  =   $m['density'];
    $inf->ext      =   (array_key_exists('ext',$m)) ? $m['ext'] : '';
    return $inf;
  }

  /**
   * Returns the name of the source of this
   * DimageName.
   * 
   * @return DimageName Source
   */
  public function source() : DimageName {
    $source = new DimageName;
    $source->entity = $this->entity;
    $source->identity = $this->identity;
    $source->index = $this->index;
    $source->ext = $this->ext;
    return $source;
  }

  /**
   * Determines if this DimageName is a source image, rather than
   * a derived image. Source images are the ones uploaded by users.
   * Derived images are images generated and resized from the source.
   */
  public function isSource() {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }
  
  /**
   * Generate a DimageName using a file path as source.
   * 
   * @param $path file path
   */
  public static function fromFilePath(string $path) : DimageName {
    return self::from(Dimage::xFileName(), $path);
  }

  /**
   * Generate a DimageName using a URL as source.
   * 
   * @param $url URL path
   * @param $ext File Extension
   * 
   * @return DimageName
   */
  public static function fromUrl(string $url, string $ext) : DimageName {
    $dimage = self::from(Dimage::xUrl(), $url);
    $dimage->ext = $ext;
    return $dimage;
  }
  
  /**
   * returns the minimzed URL Template for this image.
   * 
   * @return string URL Template
   */
  protected function urlTemplate() {
    $rurl = null;
    if ($this->isSource()) {
      if (empty($this->index)) {
        $rurl = DimageConstants::RURL_EI;
      } else {
        $rurl = DimageConstants::RURL_EIN;
      }
    } else {
      if (empty($this->index)) {
        $rurl = DimageConstants::RURL_EIPD;
      } else {
        $rurl = DimageConstants::RURL_EIPDN;
      }
    }
    if (is_null($rurl)) {
      throw new DimagesException("Unable to detect url template", 0xbd478efaaf);
    }
    return $rurl;
  }

  /**
   * Returns the file template for this object.
   * 
   * @return string File Template
   */
  protected function fileTemplate() {
    $rfile = null;
    if (empty($this->profile) || empty($this->density)) {
      $rfile = DimageConstants::RFILE_EIN;
    } else {
      $rfile = DimageConstants::RFILE_EIPDN;
    }
    return $rfile;
  }

  /**
   * Generates the URL for this DimageName.
   * 
   * @return string URL for this image
   */
  public function toUrl() : string {
    $urlTemplate = $this->urlTemplate();
    return $this->replace($urlTemplate);
  }

  /**
   * Returns the filename of this image.
   * 
   * @return string file name
   */
  public function toFileName() {
    if (empty($this->profile) || empty($this->density)) {
      return $this->replace(DimageConstants::RFILE_NAME_N);
    }
    return $this->replace(DimageConstants::RFILE_NAME_PDN);
  }

  /**
   * Returns the relative path.
   * 
   * @return string image relative path
   */
  public function toIdentityPath() : string {
    return $this->replace(DimageConstants::RFILE_PATH);
  }

  /**
   * Generates the File Name for this DimageName.
   * 
   * @return string File Name for this image
   */
  public function toIdentityPathFileName() : string {
    $rfile = $this->fileTemplate();
    return $this->replace($rfile);
  }

  /**
   * returns the full file path from the storage root
   * 
   * @return string Image file path from storage root
   */
  public function toFullPathFileName() {
    return DimageConstants::IMAGESUBDIR.'/'.$this->toIdentityPathFileName();
  }

  /**
   * returns the full image directory from the storage root
   * 
   * @return string Image directory from storage root
   */
  public function toFullPath() {
    return DimageConstants::IMAGESUBDIR.'/'.$this->toIdentityPath();
  }

  /**
   * returns the URL of the image
   * 
   * @return string Image url
   */
  public function __toString() {
    return $this->toUrl();
  }
}