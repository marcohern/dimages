<?php

namespace Marcohern\Dimages\Lib\Files;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageFolders;
use Marcohern\Dimages\Exceptions\SourceInvalidException;

class DimageFile {
  
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
      return DimageFolders::sourceFile(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->ext);
    else
      return DimageFolders::derived(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->profile, $this->density, $this->ext);
  }

  public function toFolder(): string {
    if ($this->isSource())
      return DimageFolders::sources($this->tenant, $this->entity, $this->identity);
    else
      return DimageFolders::profiles($this->tenant, $this->entity, $this->identity, $this->index, $this->profile);
  }

  public function toFileName(): string {
    if ($this->isSource())
      return DimageFolders::sourceFileName($this->index, $this->ext);
    else
      return DimageFolders::derivedFileName($this->density, $this->ext);
  }
}