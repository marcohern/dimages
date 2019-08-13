<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\DimageConstants;

class Factory {
  protected $fs;

  public function __construct(Fs $fs) {
    $this->fs = $fs;
  }

  public function dimageFile(
    string $identity, string $ext, int $index = 0,
    string $entity = DimageConstants::DFENTITY,
    string $profile = '', string $density = '',
    string $tenant=DimageConstants::DFTENANT) : DimageFile 
  {
    return new DimageFile($this->fs, $identity, $ext, $index, $entity, $profile, $density, $tenant);
  }

  public function dimageFileFromPath(string $filepath)
  {
    return DimageFile::fromFilePath($this->fs, $filepath);
  }
}