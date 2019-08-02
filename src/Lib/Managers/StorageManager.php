<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\Files\DimageFile;

class StorageManager {
  protected $scope = 'dimages';

  public function setScope($scope) {
    $this->scope = $scope;
  }

  public function url(DimageFile $dimage) : string {
    return Storage::disk($this->scope)->url($dimage->toFilePath());
  }

  public function exists(DimageFile $dimage) : bool {
    return Storage::disk($this->scope)->exists($dimage->toFilePath());
  }

  public function content(DimageFile $dimage) : string {
    return Storage::disk($this->scope)->content($dimage->toFilePath());
  }
}