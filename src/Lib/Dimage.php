<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;

class Dimage {
  private static $xFileName = null;
  private static $xFile = null;
  private static $xUrl = null;

  public static function xFileName() : string { return self::$xFileName; }
  public static function xFile() : string { return self::$xFile; }
  public static function xUrl() : string { return self::$xUrl; }

  public static function boot() {
    self::$xFileName = DimageFunctions::fileNameRegex();
    self::$xUrl = DimageFunctions::urlRegex();
    self::$xFile = DimageFunctions::regex(DimageConstants::FEXP, DimageConstants::$dimage);
    
  }

  public static function shutdown() {
    self::$xFileName = null;
    self::$xFile = null;
    self::$xUrl = null;
  }
}