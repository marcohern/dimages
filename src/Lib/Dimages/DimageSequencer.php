<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Illuminate\Support\Facades\Storage;
use Marcohern\Dimages\Lib\Dimages\DimageConstants;

class DimageSequencer {

  protected $scope;

  public function __construct($scope) {
    $this->scope = $scope;
  }

  public function nextFrom($filename) {
    $path = DimageConstants::SEQDIR."/$filename";
    $disk = Storage::disk($this->scope);
    $next = 0;
    if ($disk->exists($path)) {
      $next = 0 + $disk->get($path);
    }
    $disk->put($path, $next+1);
    return $next;
  }
}