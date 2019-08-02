<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;

class Dimage {
  private static $xFileName = null;
  private static $xUrl = null;

  public static function xFileName() : string { return self::$xFileName; }
  public static function xUrl() : string { return self::$xUrl; }

  public static function boot() {
    self::$xFileName = DimageFunctions::regex(DimageConstants::FEXP, DimageConstants::$dimage);
    self::$xUrl = DimageFunctions::urlRegex();
  }

  public static function shutdown() {
    self::$xFileName = null;
    self::$xUrl = null;
  }
}