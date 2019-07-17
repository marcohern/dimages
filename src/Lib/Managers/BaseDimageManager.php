<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageName;

class BaseDimageManager {

  private $scope;

  public function url(DimageName $dimage) : string {
    return Storage::disk($this->scope)->url($dimage->toIdentityPathFileName());
  }

  public function exists(DimageName $dimage) : bool {
    return Storage::disk($this->scope)->exists($dimage->toFullPathFileName());
  }

  public function content(DimageName $dimage) : string {
    return Storage::disk($this->scope)->get($dimage->toFullPathFileName());
  }

  public function delete(DimageName $dimage) {
    return Storage::disk($this->scope)->delete($dimage->toFullPathFileName());
  }

  public function deleteIndex($entity, $identity, $index) : int {

  }

  public function entityFolders() : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::imagesFolder());
  }

  public function identityFolders($identity) : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::entityFolder($identity));
  }

  public function images($entity, $identity) : array {
    return DimageName::fromFilePathArray(
      Storage::disk($this->scope)
        ->files(DimageFunctions::identityFolder($entity,$identity))
    );
  }
}