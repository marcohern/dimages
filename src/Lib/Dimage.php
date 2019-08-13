<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Constants;
use Marcohern\Dimages\Lib\Functions;

class Dimage {
  private static $xFile = null;

  public static function xFile() : string { return self::$xFile; }

  public static function boot() {
    self::$xFile = Functions::regex(Constants::FEXP, Constants::$dimage);
  }

  public static function shutdown() {
    self::$xFile = null;
  }
}