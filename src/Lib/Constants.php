<?php

namespace Marcohern\Dimages\Lib;

class Constants {

  public const PADINDEX  = 3;
  public const SCOPE     = 'dimages';
  public const STAGING   = '_staging';
  public const SEQUENCE  = '_sequence';
  public const DFTENANT  = '_anyone';
  public const DFENTITY  = '_anything';
  public const SETTINGS  = 'settings.cfg';
  public const LOCKS     = 'app/dimagelocks';

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