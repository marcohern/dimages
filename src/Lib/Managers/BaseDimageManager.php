<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageName;

class BaseDimageManager {

  private $scope = 'dimages';

  public function url(DimageName $dimage) : string {
    return Storage::disk($this->scope)->url($dimage->toIdentityPathFileName());
  }

  public function exists(DimageName $dimage) : bool {
    return Storage::disk($this->scope)->exists($dimage->toFullPathFileName());
  }

  public function content(DimageName $dimage) : string {
    return Storage::disk($this->scope)->get($dimage->toFullPathFileName());
  }

  public function deleteSingle(DimageName $dimage) {
    return Storage::disk($this->scope)->delete($dimage->toFullPathFileName());
  }

  public function deleteIdentity($entity, $identity) {
    $dir = DimageFunctions::identityFolder($entity,$identity);
    $disk = Storage::disk($this->scope);
    if ($disk->exists($dir)) $disk->deleteDirectory($dir);
  }

  public function entityFolders() : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::imagesFolder());
  }

  public function identityFolders($identity) : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::entityFolder($identity));
  }

  public function imageFiles($entity, $identity) : array {
    return Storage::disk($this->scope)
      ->files(DimageFunctions::identityFolder($entity,$identity));
  }

  public function dimages($entity, $identity) : array {
    $files = $this->imageFiles($entity, $identity);
    return DimageName::fromFilePathArray($files);
  }
}