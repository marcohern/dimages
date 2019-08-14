<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Functions;
use Marcohern\Dimages\Lib\Constants;
use Marcohern\Dimages\Lib\Fs;
use Marcohern\Dimages\Lib\Factory;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;
/**
 * Storage Managers for Disk.
 */
class DiskStorageManager {

  /**
   * Filesystem scope
   */
  protected $scope = Constants::SCOPE;

  /**
   * Factory
   */
  protected $factory;

  /**
   * Fs
   */
  protected $fs;

  /**
   * Constructor
   * @param Marcohern\Dimages\Lib\Factory $factory Factory
   * @param Marcohern\Dimages\Lib\Fs $fs Fs
   */
  public function __construct(Factory $factory, Fs $fs) {
    $this->fs = $fs;
    $this->factory = $factory;
  }

  /**
   * set the filesystem scope
   * @param string $scope Filesystem Scope
   */
  public function setScope(string $scope): void
  {
    $this->scope = $scope;
  }

  /**
   * Generate the url for the provided DimageFile
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File
   * @return string Dimage Url
   */
  public function url(DimageFile $dimage): string {
    return Storage::disk($this->scope)->url($dimage->toFilePath());
  }

  /**
   * Determines whether the DimageFile exists
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File
   * @return bool true if the DimageFile exists on disk, false otherwise.
   */
  public function exists(DimageFile $dimage): bool {
    return Storage::disk($this->scope)->exists($dimage->toFilePath());
  }

  /**
   * Read DimageFile content
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File
   * @return DimageFile content
   */
  public function content(DimageFile $dimage): string {
    return Storage::disk($this->scope)->get($dimage->toFilePath());
  }

  /**
   * Write content into a DimageFile.
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File
   * @param string &$content Content to write to the file
   */
  public function put(DimageFile $dimage, string &$content): void {
    Storage::disk($this->scope)->put($dimage->toFilePath(), $content);
  }

  /**
   * Delete an existint DimageFile
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File to delete
   */
  public function destroy(DimageFile $dimage): void
  {
    $disk = Storage::disk($this->scope);
    if ($disk->exists($dimage->toFilePath())) $disk->delete($dimage->toFilePath());
    else throw new DimageNotFoundException("Dimage not found");
  }

  /**
   * Delete a source and derived images given the index.
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param string $index index
   */
  public function deleteIndex(string $tenant, string $entity, string $identity, int $index): void
  {
    $disk = Storage::disk($this->scope);
    $folder = $this->fs->indexFolder($tenant, $entity, $identity, $index);
    $sourceFolder = $this->fs->identityFolder($tenant, $entity, $identity);
    $files = $disk->files($sourceFolder);
    foreach ($files as $file) {
      $dimage = $this->factory->dimageFileFromPath($file);
      if ($dimage->index === $index) {
        $this->destroy($dimage);
        break;
      }
    }
    if ($disk->exists($folder)) $disk->deleteDirectory($folder);
  }

  public function deleteSingle(DimageFile $dimage) : void {
    Storage::disk($this->scope)->delete($dimage->toFilePath());
  }

  public function deleteMultiple(array $dimages) : void {
    $files = Functions::toFilePaths($dimages);
    Storage::disk($this->scope)->delete($files);
  }

  public function move(DimageFile $source, DimageFile $target) : void {
    Storage::disk($this->scope)->move($source->toFilePath(), $target->toFilePath());
  }

  public function attach($tenant, $session, $targetEntity, $targetIdentity):void {
    $source = $this->fs->stagingSessionFolder($tenant, $session);
    $target = $this->fs->identityFolder($tenant, $targetEntity, $targetIdentity);
    Storage::disk($this->scope)->move($source, $target);
  }

  public function store(DimageFile $dimage, UploadedFile $upload) {
    Storage::disk($this->scope)
      ->putFileAs($dimage->toFolder(), $upload, $dimage->toFileName());
  }

  public function deleteIdentity(string $tenant,string $entity,string $identity):void {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  public function deleteStaging(string $tenant, string $session) : void {
    $folder = $this->fs->stagingSessionFolder($tenant, $session);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  public function deleteStagingForTenants(array $tenants) : void {
    foreach ($tenants as $tenant) {
      $folder = $this->fs->stagingFolder($tenant);
      Storage::disk($this->scope)->deleteDirectory($folder);
    }
  }

  public function tenants() : array {
    return Storage::disk($this->scope)->directories("/");
  }

  public function entities(string $tenant) : array {
    $folder = $this->fs->tenantFolder($tenant);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  public function identities(string $tenant, string $entity) : array {
    $folder = $this->fs->entityFolder($tenant, $entity);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  public function sources(string $tenant, string $entity, string $identity) : array {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    return Storage::disk($this->scope)->files($folder);
  }

  public function indexes(string $tenant, string $entity, string $identity) : array {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    return Storage::disk($this->scope)->directories($folder);
  }

  public function profiles(string $tenant, string $entity, string $identity, int $index) : array {
    $folder = $this->fs->indexFolder($tenant, $entity, $identity, $index);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  public function derivatives(string $tenant, string $entity, string $identity, int $index, string $profile) : array {
    $folder = $this->fs->profileFolder($tenant, $entity, $identity, $index, $profile);
    return Storage::disk($this->scope)->files($folder);
  }

  public function switchIndex(string $tenant, string $entity, string $identity, int $source, int $target) : void {
    $disk = Storage::disk($this->scope);
    $files = $this->sources($tenant, $entity, $identity);
    $sourceDimage = null;
    $sourceIndexFolder = null;
    $targetDimage = null;
    $targetIndexFolder = $this->fs->indexFolder($tenant, $entity, $identity, $target);
    foreach ($files as $file) {
      $dimage = $this->factory->dimageFileFromPath($file);
      if ($dimage->index === $source) {
        $sourceDimage = $dimage;
        $sourceIndexFolder = $this->fs->indexFolder($tenant, $entity, $identity, $source);
      } else if ($dimage->index === $target) {
        $targetDimage = $dimage;
      }
      if (!is_null($sourceDimage) && !is_null($targetDimage)) break;
    }

    if (is_null($sourceDimage)) throw new DimageNotFoundException("source file not found");
    if (is_null($targetDimage)) {
      $targetDimage = clone $sourceDimage;
      $targetDimage->index = $target;
      $this->move($sourceDimage, $targetDimage);
      if ($disk->exists($sourceIndexFolder)) $disk->move($sourceIndexFolder, $targetIndexFolder);
    } else {
      $tmpDimage = $this->factory->dimageFile($identity, 'tmpx', $source, $entity, '', '', $tenant);
      $tmpFolder = $this->fs->indexFolder($tenant, $entity, $identity, $source + 1000);

      $this->move($targetDimage, $tmpDimage);
      $this->move($sourceDimage, $targetDimage);
      $this->move($tmpDimage, $sourceDimage);

      if ($disk->exists($sourceIndexFolder)) {
        if ($disk->exists($targetIndexFolder)) {
          $disk->move($targetIndexFolder, $tmpFolder);
          $disk->move($sourceIndexFolder, $targetIndexFolder);
          $disk->move($tmpFolder, $sourceIndexFolder);
        } else {
          $disk->move($sourceIndexFolder, $targetIndexFolder);
        }
      } else if ($disk->exists($targetIndexFolder)) {
        $disk->move($targetIndexFolder, $sourceIndexFolder);
      }
    }
  }
}