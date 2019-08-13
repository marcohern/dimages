<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;

class Functions {

  public static function findVariables($source) {
    $m = [];
    $r = [];
    preg_match_all("/%(\w+)/", $source, $m);
    return array_unique($m[1]);
  }
  
  public static function regex(string $source, array &$expressions) : string {
    do {
      $vars = self::findVariables($source);
      foreach ($vars as $var) {
        $value = $expressions[$var];
        $source = str_replace("%$var", $value, $source);
      }
    } while (count($vars) > 0);
    
    return "/$source/";
  }

  /**
   * Pad a number with zeroes to the left. Example 5 -> 00005
   * 
   * @param $number Number
   * @param $n Amount of padding
   * @return string Padded number
   */
  public static function pad($number, $n) : string {
    return str_pad($number, $n, '0', STR_PAD_LEFT);
  }

  /**
   * Pad an image index. Example 2 -> 002
   * 
   * @param $index Index
   * @return string Padded index
   */
  public static function padIndex($index) : string {
    return self::pad($index, DimageConstants::PADINDEX);
  }

  /**
   * Convert a list of dimages to file paths
   * 
   * @param $dimages list of images
   * @return array list of file paths
   */
  public static function toFilePaths(array &$dimages) : array {
    $result = [];
    foreach ($dimages as $dimage) {
      if ($dimage instanceof DimageName)
        $result[] = $dimage->toFullPathFileName();
      else
        $result[] = $dimage->toFilePath();
    }
    return $result;
  }

  public static function suffix(array &$strings,int $len) : array {
    $entities = [];
    foreach ($strings as $s) {
      $entities[] = substr($s, $len);
    }
    return $entities;
  }
}