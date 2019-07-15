<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Marcohern\Dimages\Lib\Dimages\DimageConstants;
use Marcohern\Dimages\Lib\Dimages\DimageFunctions;

class Dimage {
  private static $xFileName = null;
  private static $xUrl = null;

  public static function xFileName() : string { return self::$xFileName; }
  public static function xUrl() : string { return self::$xUrl; }

  public static function boot() {
    self::$xFileName = DimageFunctions::fileNameRegex();
    self::$xUrl = DimageFunctions::urlRegex();
  }

  public static function shutdown() {
    self::$xFileName = null;
    self::$xUrl = null;
  }
}