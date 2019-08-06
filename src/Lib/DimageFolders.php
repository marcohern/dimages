<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageFunctions;

class DimageFolders {
  protected static function images(string $suffix): string
  {
    return $suffix; 
  }

  public static function entities(string $tenant): string
  {
    return self::images($tenant); 
  }

  public static function identities(string $tenant, string $entity): string
  {
    return self::images("$tenant/$entity"); 
  }
  
  public static function sources(string $tenant, string $entity, string $identity): string
  {
    return self::images("$tenant/$entity/$identity"); 
  }
  
  public static function profiles(string $tenant, string $entity, string $identity, int $index): string
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex"); 
  }
  
  public static function densities(string $tenant, string $entity, string $identity, int $index, string $profile): string
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex/$profile");
  }
  
  public static function sourceFile(string $tenant, string $entity, string $identity, int $index, string $ext): string
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex.$ext"); 
  }
  
  public static function sourceFileName(int $index, string $ext): string
  {
    $pindex = DimageFunctions::padIndex($index);
    return "$pindex.$ext";
  }
  
  public static function derivedFileName(string $density, string $ext): string
  {
    return "$density.$ext";
  }

  public static function derivatives(
    string $tenant, 
    string $entity, string $identity, int $index,
    string $profile) 
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex/$profile");
  }

  public static function sequenceFile(string $tenant, string $entity, string $identity) {
    return self::images("$tenant/_seq/$entity.$identity.id");
  }

  public static function stagingFolder(string $tenant) {
    return self::images("$tenant/_tmp");
  }

  public static function staging(string $tenant, string $session) {
    return self::images("$tenant/_tmp/$session");
  }

  public static function derived(
    string $tenant,
    string $entity, string $identity,
    int $index,
    string $profile, string $density,
    string $ext)
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex/$profile/$density.$ext");
  }
}