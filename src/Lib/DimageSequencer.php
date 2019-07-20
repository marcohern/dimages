<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Support\Facades\Storage;
use Marcohern\Dimages\Lib\DimageConstants;

/**
 * Generate an index sequence for images
 */
class DimageSequencer {

  /**
   * Storage scope
   */
  protected $scope;

  /**
   * Sub folder where sequences are stored
   */
  protected $subdir;

  /**
   * File name of a sequence
   */
  protected $filename;

  /**
   * Constructor
   * 
   * @param $entity Entity
   * @param $identity Identity
   */
  public function __construct($entity, $identity) {
    $this->scope = DimageConstants::FSSCOPE;
    $this->subdir = DimageConstants::SEQDIR;
    $this->filename = "$entity.$identity.id";
  }

  /**
   * The the current value of a sequence.
   */
  public function get() {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    if ($disk->exists($path)) {
      return 0 + $disk->get($path);
    }
    return 0;
  }

  /**
   * Set the value of a sequence.
   * 
   * @param $n sequence value
   */
  public function put($n) {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    $disk->put($path, $n);
  }

  /**
   * Get the next value in the sequence
   * 
   * @return int next value in sequence
   */
  public function next() : int {
    $next = $this->get();
    $this->put($next+1);
    return $next;
  }

  /**
   * Delete the sequence file
   */
  public function drop() {
    $path = "{$this->subdir}/{$this->filename}";
    $disk = Storage::disk($this->scope);
    if ($disk->exists($path)) {
      $disk->delete($path);
    }
  }
}