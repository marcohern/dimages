<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageConstants;

class DimageFunctions {

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

  public static function pad($number, $n) : string {
    return str_pad($number, $n, '0', STR_PAD_LEFT);
  }

  public static function padIndex($index) : string {
    return self::pad($index, DimageConstants::PADINDEX);
  }

  public static function rootFolder() : string {
    return DimageConstants::IMAGESUBDIR;
  }

  public static function entityFolder($entity) : string {
    $folder = DimageConstants::RFILE_SUPERPATH;
    $folder = str_replace('%entity'  , $entity  , $folder);
    return DimageConstants::IMAGESUBDIR.'/'.$folder;
  }

  public static function identityFolder($entity, $identity) : string {
    $folder = DimageConstants::RFILE_PATH;
    $folder = str_replace('%entity'  , $entity  , $folder);
    $folder = str_replace('%identity', $identity, $folder);
    return DimageConstants::IMAGESUBDIR.'/'.$folder;
  }

  public static function toFilePaths(array &$dimages) : array {
    $result = [];
    foreach ($dimages as $dimage) $result[] = $dimage->toFullPathFileName();
    return $result;
  }
}