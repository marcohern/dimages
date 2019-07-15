<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Marcohern\Dimages\Lib\Dimages\DimageConstants;

class DimageFunctions {

  public static function basePathRegex() {
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

  public static function fileNameRegex() {
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

  public static function urlRegex() {
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

  public static function pad($number, $n) {
    return str_pad($number, $n, '0', STR_PAD_LEFT);
  }

  public static function padIndex($index) {
    return self::pad($index, DimageConstants::PADINDEX);
  }

  public static function imgFolder($entity, $identity) {
    $folder = DimageConstants::RFILE_PATH;
    $folder = str_replace('%entity'  , $entity  , $folder);
    $folder = str_replace('%identity', $identity, $folder);
    return DimageConstants::IMAGESUBDIR.'/'.$folder;
  }
}