<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\DimageName;

/**
 * Manage dimage storage.
 */
class StorageDimageManager {

  /**
   * Storage Scope
   */
  protected $scope;

  /**
   * Get Dimage URL according to filesystem settings
   * 
   * @param $dimage Dimage Name
   * @return string Image URL
   */
  public function url(DimageName $dimage) : string {
    return Storage::disk($this->scope)->url($dimage->toIdentityPathFileName());
  }

  /**
   * Returns if a Dimage exists
   * 
   * @param $dimage Dimage Name
   * @return bool True if it exists, false otherwise.
   */
  public function exists(DimageName $dimage) : bool {
    return Storage::disk($this->scope)->exists($dimage->toFullPathFileName());
  }

  /**
   * Return a Dimage Content
   * 
   * @param $dimage Dimage Name
   * @return string Image Content
   */
  public function content(DimageName $dimage) : string {
    return Storage::disk($this->scope)->get($dimage->toFullPathFileName());
  }

  /**
   * Delete a single Dimage
   * 
   * @param $dimage Dimage Name
   */
  public function deleteSingle(DimageName $dimage) {
    Storage::disk($this->scope)->delete($dimage->toFullPathFileName());
  }

  /**
   * Delete multiple Dimages
   * 
   * @param $dimages Array of DimageName
   */
  public function deleteMultiple(array $dimages) {
    $files = DimageFunctions::toFilePaths($dimages);
    Storage::disk($this->scope)->delete($files);
  }

  /**
   * Delete all images associated with an identity.
   * 
   * @param $entity Entity of the Dimage
   * @param $udentity Identity of the Dimage
   */
  public function deleteIdentity(string $entity, string $identity) {
    $dir = DimageFunctions::identityFolder($entity,$identity);
    $disk = Storage::disk($this->scope);
    if ($disk->exists($dir)) $disk->deleteDirectory($dir);
  }

  /**
   * Get a list of available entities
   * 
   * @return array list of entities
   */
  public function entities() : array {
    $dir = DimageFunctions::rootFolder();
    $dlen = strlen($dir) + 1;
    $folders = Storage::disk($this->scope)->directories($dir);
    foreach ($folders as $i => $folder) {
      $folders[$i] = substr($folder, $dlen);
    }
    return $folders;
  }

  /**
   * Get a list of available identities
   * 
   * @param $entity Entity of dimages
   * @return array List of identities inside entity
   */
  public function identities(string $entity) : array {
    $dir = DimageFunctions::entityFolder($entity);
    $dlen = strlen($dir) + 1;
    $folders = Storage::disk($this->scope)->directories($dir);
    foreach ($folders as $i => $folder) {
      $folders[$i] = substr($folder, $dlen);
    }
    return $folders;
  }

  /**
   * Get a list of files of a single identity
   * 
   * @param $entity Entity
   * @param $identity Identity
   * @param $index (optional)
   * @return array List of file names
   */
  public function files(string $entity, string $identity, $index = null) : array {
    if (is_null($index))
      return Storage::disk($this->scope)
        ->files(DimageFunctions::identityFolder($entity,$identity));
    else {
      $rfiles = [];
      $files = Storage::disk($this->scope)
        ->files(DimageFunctions::identityFolder($entity,$identity));
      foreach ($files as $file) {
        $dimage = DimageName::fromFilePath($file);
        if ($dimage->index === $index) $rfiles[] = $file;
      }
      return $rfiles;
    }
  }

  /**
   * Return a list of Dimage names in identity
   * 
   * @param $entity Entity
   * @param $identity Identity
   * @param $index (optional)
   * @return array List of dimage names
   */
  public function dimages(string $entity, string $identity, $index = null) : array {
    $files = $this->files($entity, $identity, $index);
    return DimageName::fromFilePathArray($files);
  }

  /**
   * Append a newly uploaded image
   * 
   * @param $dimage Dimage Name
   * @param $upload File Upload
   */
  public function store(DimageName $dimage, UploadedFile $upload) {
    Storage::disk($this->scope)
      ->putFileAs($dimage->toFullPath(), $upload, $dimage->toFileName());
  }

  /**
   * Move a Dimage
   * 
   * @param $source Source name
   * @param $target Destination name
   */
  public function move(DimageName $source, DimageName $target) {
    Storage::disk($this->scope)
      ->move($source->toFullPathFileName(), $target->toFullPathFileName());
  }

  /**
   * Put or Write content into a DimageName
   * 
   * @param $dimage Dimage Name to write content as
   * @param $content Content
   */
  public function put(DimageName $dimage, &$content) {
    Storage::disk($this->scope)
      ->put($dimage->toFullPathFileName(), $content);
  }
}