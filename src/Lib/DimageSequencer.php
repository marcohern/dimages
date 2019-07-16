<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Support\Facades\Storage;
use Marcohern\Dimages\Lib\DimageConstants;

class DimageSequencer {

  protected $scope;
  protected $subdir;
  protected $filename;

  public function __construct($entity, $identity) {
    $this->scope = DimageConstants::FSSCOPE;
    $this->subdir = DimageConstants::SEQDIR;
    $this->filename = "$entity.$identity.id";
  }

  public function get() {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    if ($disk->exists($path)) {
      return 0 + $disk->get($path);
    }
    return 0;
  }

  public function put($n) {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    $disk->put($path, $n);
  }

  public function next() {
    $next = $this->get();
    $this->put($next+1);
    return $next;
  }

  public function drop() {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    if ($disk->exists($path)) {
      $disk->delete($path);
    }
  }
}