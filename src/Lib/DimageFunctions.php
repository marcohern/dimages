<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;

class DimageFunctions {

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
   * Return the Regular Expression that matches the base path
   * for any image (entity/identity)
   * 
   * @return string Base Path Regex
   */
  public static function basePathRegex() : string {
    $xidf = DimageConstants::XIDF;

    $xentity = DimageConstants::XENTITY;
    $xentity = str_replace('%idf', $xidf, $xentity);

    $xidentity = DimageConstants::XIDENTITY;
    $xidentity = str_replace('%idf', $xidf, $xidentity);

    $xbasepath = DimageConstants::XBASEPATH;
    $xbasepath = str_replace('%entity'  , $xentity  , $xbasepath);
    $xbasepath = str_replace('%identity', $xidentity, $xbasepath);
    return $xbasepath;
  }

  /**
   * Return the Regular Expression that matches the file name
   * for any image (index.profile.density.ext)
   * 
   * @return string File Name Regex
   */
  public static function fileNameRegex() : string {
    $xint = DimageConstants::XINT;
    $xidf = DimageConstants::XIDF;

    $xindex = DimageConstants::XINDEX;
    $xindex = str_replace('%int', $xint, $xindex);

    $xprofile = DimageConstants::XPROFILE;
    $xprofile = str_replace('%idf', $xidf, $xprofile);

    $xdensity = DimageConstants::XDENSITY;
    $xdensity = str_replace('%idf', $xidf, $xdensity);

    $xext = DimageConstants::XEXT;
    $xext = str_replace('%idf', $xidf, $xext);

    $xbasepath = self::basePathRegex();
    
    $xfilename = DimageConstants::XFILENAME;
    $xfilename = str_replace('%index'  , $xindex  , $xfilename);
    $xfilename = str_replace('%profile', $xprofile, $xfilename);
    $xfilename = str_replace('%density', $xdensity, $xfilename);
    $xfilename = str_replace('%ext'    , $xext    , $xfilename);
    
    $exp = DimageConstants::XFILE;
    $exp = str_replace('%basepath', $xbasepath, $exp);
    $exp = str_replace('%filename', $xfilename, $exp);

    return "/$exp/";
  }

  /**
   * Return the Regular Expression the url
   * for any image (entity/identity/profile/density/index)
   * 
   * @return string File Name Regex
   */
  public static function urlRegex() : string {
    $xint = DimageConstants::XINT;
    $xidf = DimageConstants::XIDF;

    $xindex = DimageConstants::XINDEX;
    $xindex = str_replace('%int', $xint, $xindex);

    $xprofile = DimageConstants::XPROFILE;
    $xprofile = str_replace('%idf', $xidf, $xprofile);

    $xdensity = DimageConstants::XDENSITY;
    $xdensity = str_replace('%idf', $xidf, $xdensity);

    $xext = DimageConstants::XEXT;
    $xext = str_replace('%idf', $xidf, $xext);

    $xbasepath = self::basePathRegex();

    $xurlsuffx = DimageConstants::XURLSUFFX;
    $xurlsuffx = str_replace('%index'  , $xindex  , $xurlsuffx);
    $xurlsuffx = str_replace('%profile', $xprofile, $xurlsuffx);
    $xurlsuffx = str_replace('%density', $xdensity, $xurlsuffx);
    $xurlsuffx = str_replace('%ext'    , $xext    , $xurlsuffx);

    $exp = DimageConstants::XURL;
    $exp = str_replace('%basepath', $xbasepath, $exp);
    $exp = str_replace('%urlsuffx', $xurlsuffx, $exp);
    return "/$exp/";
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
   * Return the image root folder
   * 
   * @param string root folder
   */
  public static function rootFolder() : string {
    return DimageConstants::IMAGESUBDIR;
  }

  /**
   * Return the entity folder
   * 
   * @param $entity Entity
   * @return string entity folder
   */
  public static function entityFolder($entity) : string {
    $folder = DimageConstants::RFILE_SUPERPATH;
    $folder = str_replace('%entity'  , $entity  , $folder);
    return DimageConstants::IMAGESUBDIR.'/'.$folder;
  }

  /**
   * Generate the propper identity folder
   * 
   * @param $entity Enitty
   * @param $identity Identity
   * @return string identity folder
   */
  public static function identityFolder($entity, $identity) : string {
    $folder = DimageConstants::RFILE_PATH;
    $folder = str_replace('%entity'  , $entity  , $folder);
    $folder = str_replace('%identity', $identity, $folder);
    return DimageConstants::IMAGESUBDIR.'/'.$folder;
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