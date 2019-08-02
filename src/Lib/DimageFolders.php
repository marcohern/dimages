<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\DimageFunctions;

class DimageFolders {
  protected static function images(string $suffix): string
  {
    return "img/$suffix"; 
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
  
  public static function source(string $tenant, string $entity, string $identity, int $index): string
  {
    $pindex = DimageFunctions::padIndex($index);
    return self::images("$tenant/$entity/$identity/$pindex"); 
  }

  public static function profile(
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

  public static function staging(string $tenant, string $session) {
    return self::images("$tenant/_tmp/$session");
  }
}