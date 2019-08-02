<?php

namespace Marcohern\Dimages\Lib\Files;

use Marcohern\Dimages\Lib\DimageFolders;

class DimageFile {
  
  public $tenant = '_global';
  public $entity;
  public $identity;
  public $index;
  public $profile = '';
  public $density = '';
  public $ext;

  public function __construct($entity, $identity, $index, $ext, $profile='', $density='') {
    $this->entity = $entity;
    $this->identity = $identity;
    $this->index = $index;
    $this->ext = $ext;
    $this->profile = $profile;
    $this->density = $density;
  }

  public function isSource(): bool {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }

  public function isDerived(): bool {
    return !$this->isSource();
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
      return DimageFolders::profile($this->tenant, $this->entity, $this->identity, $this->index, $this->profile);
  }

  public function toFileName(): string {
    if ($this->isSource())
      return DimageFolders::sourceFileName($this->index, $this->ext);
    else
      return DimageFolders::derivedFileName($this->density, $this->ext);
  }
}