<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\Naming\ExportableDimageName;
use Marcohern\Dimages\Exceptions\SourceInvalidException;

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
class DimageName extends ExportableDimageName {

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
      throw new SourceInvalidException("source invalid: $haystack.", 0xa996a53d53);
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
   * Generate a DimageName using a file path as source.
   * 
   * @param $path file path
   */
  public static function fromFilePath(string $path) : DimageName {
    return self::from(Dimage::xFileName(), $path);
  }

  public static function fromFilePathArray(array &$paths) : array {
    $dimages = [];
    foreach ($paths as $path) {
      $dimages[] = self::fromFilePath($path);
    }
    return $dimages;
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
   * returns the URL of the image
   * 
   * @return string Image url
   */
  public function __toString() {
    return $this->toUrl();
  }
}