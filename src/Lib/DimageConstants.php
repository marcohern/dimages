<?php

namespace Marcohern\Dimages\Lib;

class DimageConstants {

  public const PADINDEX = 3;
  public const FSSCOPE = 'dimages';
  public const FSPATH = 'app/dimages';
  public const DIMROUTE  = '/mh/dim/api';
  
  public const XIDF = '[A-Za-z_][\w\-_]*';
  public const XINT = '\d+';

  public const XENTITY   = '(?<entity>%idf)';
  public const XIDENTITY = '(?<identity>%idf)';
  public const XINDEX    = '(?<index>%int)';
  public const XPROFILE  = '(?<profile>%idf)';
  public const XDENSITY  = '(?<density>%idf)';
  public const XEXT      = '(?<ext>%idf)';

  public const XBASEPATH = '%entity\/%identity';
  public const XFILENAME = '%index(\.%profile\.%density)?\.%ext';
  public const XURLSUFFX = '(\/%profile\/%density)?\/(%index)?';

  public const XFILE = '%basepath\/%filename';
  public const XURL = '%basepath(%urlsuffx)?';

  public const DEFAULT_ENTITY = '_global';

  public const RFILE_EIPDN    = '%entity/%identity/%pindex.%profile.%density.%ext';
  public const RFILE_EIN      = '%entity/%identity/%pindex.%ext';
  public const RFILE_PATH     = '%entity/%identity';
  public const RFILE_NAME_PDN = '%pindex.%profile.%density.%ext';
  public const RFILE_NAME_N   = '%pindex.%ext';

  public const RURL_EIPDN  = '%entity/%identity/%profile/%density/%index';
  public const RURL_EIPD   = '%entity/%identity/%profile/%density';
  public const RURL_EIN    = '%entity/%identity/%index';
  public const RURL_EI     = '%entity/%identity';
  public const RURL_I      = '%identity';

  public const SEQDIR      = 'seqs';

  public const IMAGESUBDIR = 'img';
  
}