<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;

class Dimage {
  private static $xFile = null;

  public static function xFile() : string { return self::$xFile; }

  public static function boot() {
    self::$xFile = DimageFunctions::regex(DimageConstants::FEXP, DimageConstants::$dimage);
    
  }

  public static function shutdown() {
    self::$xFile = null;
  }
}