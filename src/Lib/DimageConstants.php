<?php

namespace Marcohern\Dimages\Lib;

class DimageConstants {

  public const PADINDEX = 3;
  public const FSSCOPE = 'dimages';
  public const DIMROUTE  = '/dimages';

  public static $dimage = [
    'tenant'   => '(?<tenant>%idf)',
    'entity'   => '(?<entity>%idf)',
    'identity' => '(?<identity>%idf)',
    'index'    => '(?<index>%int)',
    'profile'  => '(?<profile>%idf)',
    'density'  => '(?<density>%idf)',
    'ext'      => '(?<ext>%idf)',
    'idf'      => '[\w\-_\.@]*',
    'int'      => '\d+',
  ];
  public const FEXP = '%tenant\/%entity\/%identity\/%index(\/%profile\/%density)?\.%ext';
  
}