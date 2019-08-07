<?php

namespace Marcohern\Dimages\Lib\Files;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Exceptions\SourceInvalidException;

use Marcohern\Dimages\Lib\Fs;
class DimageFile {
  
  protected $fs;

  public $tenant = '_global';
  public $entity;
  public $identity;
  public $index;
  public $profile = '';
  public $density = '';
  public $ext;

  public static function fromFilePath($filepath):DimageFile {
    $m = null;
    $r = preg_match(Dimage::xFile(),$filepath, $m);
    if (!$r) {
      throw new SourceInvalidException("source invalid: $haystack.", 0xa996a53d53);
    }
    $m = (object)$m;
    
    return new DimageFile($m->entity, $m->identity, 0+$m->index, $m->ext, $m->profile, $m->density, $m->tenant);
  }

  public function __construct($entity, $identity, $index, $ext, $profile='', $density='', $tenant='_global') {
    $this->entity = $entity;
    $this->identity = $identity;
    $this->index = $index;
    $this->ext = $ext;
    $this->profile = $profile;
    $this->density = $density;
    $this->tenant = $tenant;

    $this->fs = Fs::getInstance();
  }

  public function isSource(): bool {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }

  public function isDerived(): bool {
    return !$this->isSource();
  }

  public function source(): DimageFile {
    return new DimageFile(
      $this->entity, $this->identity, $this->index,
      $this->ext, '', '', $this->tenant);
  }

  public function toFilePath(): string {
    if ($this->isSource())
      return $this->fs->sourcePath(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->ext);
    else
      return $this->fs->derivedPath(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->profile, $this->density, $this->ext);
  }

  public function toFolder(): string {
    if ($this->isSource())
      return $this->fs->identityFolder($this->tenant, $this->entity, $this->identity);
    else
      return $this->fs->profileFolder($this->tenant, $this->entity, $this->identity, $this->index, $this->profile);
  }

  public function toFileName(): string {
    if ($this->isSource())
      return $this->fs->sourceFile($this->index, $this->ext);
    else
      return $this->fs->derivedFile($this->density, $this->ext);
  }
}