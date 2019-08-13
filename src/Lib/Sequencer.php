<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Support\Facades\Storage;

/**
 * Generate an index sequence for images
 */
class Sequencer {

  /**
   * Storage scope
   */
  protected $scope = Constants::SCOPE;

  /*
   * File system access
   */
  protected $fs;

  /**
   * Name and path to file sequence
   */
  protected $filepath;

  /**
   * Constructor
   * 
   * @param $entity Entity
   * @param $identity Identity
   */
  public function __construct(
    Fs $fs, string $identity,
    string $entity = Constants::DFENTITY,
    string $tenant = Constants::DFTENANT
  ) {
    $this->fs = $fs;
    $this->filepath = $this->fs->sequencePath($tenant, $entity, $identity);
  }

  /**
   * The the current value of a sequence.
   */
  public function get() {
    $disk = Storage::disk($this->scope);
    if ($disk->exists($this->filepath)) {
      return 0 + $disk->get($this->filepath);
    }
    return 0;
  }

  /**
   * Set the value of a sequence.
   * 
   * @param $n sequence value
   */
  public function put($n) {
    $disk = Storage::disk($this->scope);
    $disk->put($this->filepath, $n);
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
    $disk = Storage::disk($this->scope);
    if ($disk->exists($this->filepath)) {
      $disk->delete($this->filepath);
    }
  }
}