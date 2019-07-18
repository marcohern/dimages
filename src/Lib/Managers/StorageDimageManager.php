<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\DimageName;

class StorageDimageManager {

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

  public function deleteMultiple(array $dimages) {
    $files = DimageFunctions::toFilePaths($dimages);
    Storage::disk($this->scope)->delete($files);
  }

  public function deleteIdentity(string $entity, string $identity) {
    $dir = DimageFunctions::identityFolder($entity,$identity);
    $disk = Storage::disk($this->scope);
    if ($disk->exists($dir)) $disk->deleteDirectory($dir);
  }

  public function entities() : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::rootFolder());
  }

  public function identities(string $identity) : array {
    return Storage::disk($this->scope)
      ->directories(DimageFunctions::entityFolder($identity));
  }

  public function files(string $entity, string $identity) : array {
    return Storage::disk($this->scope)
      ->files(DimageFunctions::identityFolder($entity,$identity));
  }

  public function dimages(string $entity, string $identity) : array {
    $files = $this->files($entity, $identity);
    return DimageName::fromFilePathArray($files);
  }

  public function storeDirect(DimageName $dimage, UploadedFile $upload) {
    Storage::disk($this->scope)
      ->putFileAs($dimage->toFullPath(), $upload, $dimage->toFileName());
  }

  public function move(DimageName $source, DimageName $target) {
    Storage::disk($this->scope)
      ->move($source->toFullPathFileName(), $target->toFullPathFileName());
  }
}