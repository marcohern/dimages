<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Marcohern\Dimages\Lib\Dimages\Dimage;
use Marcohern\Dimages\Lib\Dimages\DimageConstants;
use Marcohern\Dimages\Exceptions\ImageException;

class DimageName {

  public $entity;
  public $identity;
  public $index;
  public $profile = '';
  public $density = '';
  public $ext;

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

  protected static function from(string $needle, string $haystack) : DimageName {
    $m = null;
    $r = preg_match($needle, $haystack, $m);
    if (!$r) {
      throw new ImageException("source invalid: $haystack.", 0xa996a53d53);
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

  public function isSource() {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }

  public static function fromFilePath(string $path) : DimageName {

    return self::from(Dimage::xFileName(), $path);
  }

  public static function fromUrl(string $url, string $ext) : DimageName {
    $dimage = self::from(Dimage::xUrl(), $url);
    $dimage->ext = $ext;
    return $dimage;
  }
  
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
      throw new ImageException("Unable to detect url template", 0xbd478efaaf);
    }
    return $rurl;
  }

  protected function fileTemplate() {
    $rfile = null;
    if (empty($this->profile) || empty($this->density)) {
      $rfile = DimageConstants::RFILE_EIN;
    } else {
      $rfile = DimageConstants::RFILE_EIPDN;
    }
    return $rfile;
  }

  public function toUrl() : string {
    $urlTemplate = $this->urlTemplate();
    return $this->replace($urlTemplate);
  }

  public function toFileName() : string {
    $rfile = $this->fileTemplate();
    return $this->replace($rfile);
  }

  public function getPath() : string {
    return $this->replace(DimageConstants::RFILE_PATH);
  }

  public function getName() {
    if (empty($this->profile) || empty($this->density)) {
      return $this->replace(DimageConstants::RFILE_NAME_N);
    }
    return $this->replace(DimageConstants::RFILE_NAME_PDN);
  }

  public function source() : DimageName {
    $source = new DimageName;
    $source->entity = $this->entity;
    $source->identity = $this->identity;
    $source->index = $this->index;
    $source->ext = $this->ext;
    return $source;
  }

  public function __toString() {
    return $this->toUrl();
  }

  public function url() {
    return DimageConstants::IMAGESUBDIR.'/'.$this->toFileName();
  }
}