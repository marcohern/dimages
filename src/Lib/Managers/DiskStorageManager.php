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
   * @param int $index index
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

  /**
   * Delete multiple dimages.
   * @param array $dimages array of type DimageFile.
   */
  public function deleteMultiple(array $dimages): void
  {
    $files = Functions::toFilePaths($dimages);
    Storage::disk($this->scope)->delete($files);
  }

  /**
   * Move a DimageFile from a source to a target
   * @param Marcohern\Dimages\Lib\DimageFile $source Source Dimage File
   * @param Marcohern\Dimages\Lib\DimageFile $target Target Dimage File
   */
  public function move(DimageFile $source, DimageFile $target): void
  {
    Storage::disk($this->scope)->move($source->toFilePath(), $target->toFilePath());
  }

  /**
   * Move a DimageFile from staging to a specific entity/identity.
   * @param string $tenant Tenant
   * @param string $session Staging session Id
   * @param string $entity Entity
   * @param string $identity Identity
   */
  public function attach(string $tenant, string $session, string $entity, string $identity): void
  {
    $source = $this->fs->stagingSessionFolder($tenant, $session);
    $target = $this->fs->identityFolder($tenant, $entity, $identity);
    Storage::disk($this->scope)->move($source, $target);
  }

  /**
   * Save an uploaded file as a DimageFile
   * @param Marcohern\Dimages\Lib\DimageFile $dimage Dimage File
   * @param Illuminate\Http\UploadedFile $upload Uploaded File
   */
  public function store(DimageFile $dimage, UploadedFile $upload): void {
    Storage::disk($this->scope)
      ->putFileAs($dimage->toFolder(), $upload, $dimage->toFileName());
  }

  /**
   * Delete all identity images.
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   */
  public function deleteIdentity(string $tenant, string $entity, string $identity): void {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  /**
   * Delete all identity images.
   * @param string $tenant Tenant
   * @param string $session Session
   */
  public function deleteStaging(string $tenant, string $session): void {
    $folder = $this->fs->stagingSessionFolder($tenant, $session);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  /**
   * Delete staging folder for specified tenants
   * @param array $tenants Tenants
   */
  public function deleteStagingForTenants(array $tenants): void {
    foreach ($tenants as $tenant) {
      $folder = $this->fs->stagingFolder($tenant);
      Storage::disk($this->scope)->deleteDirectory($folder);
    }
  }

  /**
   * Return a list of tenants.
   * @return array Tenants
   */
  public function tenants(): array {
    return Storage::disk($this->scope)->directories("/");
  }

  /**
   * List of entities
   * @param string $tenant Tenant
   * @return array Entities within tenant
   */
  public function entities(string $tenant): array {
    $folder = $this->fs->tenantFolder($tenant);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  /**
   * List of identities
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @return array List of identities within entity
   */
  public function identities(string $tenant, string $entity): array {
    $folder = $this->fs->entityFolder($tenant, $entity);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  /**
   * List of source files
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @return array List of source image files
   */
  public function sources(string $tenant, string $entity, string $identity): array {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    return Storage::disk($this->scope)->files($folder);
  }

  /**
   * List of index directories
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @return array List of index directories
   */
  public function indexes(string $tenant, string $entity, string $identity): array {
    $folder = $this->fs->identityFolder($tenant, $entity, $identity);
    return Storage::disk($this->scope)->directories($folder);
  }

  /**
   * List of available profiles
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param int $index Index
   * @return array List of generated profiles
   */
  public function profiles(string $tenant, string $entity, string $identity, int $index): array {
    $folder = $this->fs->indexFolder($tenant, $entity, $identity, $index);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return Functions::suffix($subfolders, strlen($folder)+1);
  }

  /**
   * List of derivative images
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param int $index Index
   * @param string $profile Profile
   * @return array List of derivative image files
   */
  public function derivatives(string $tenant, string $entity, string $identity, int $index, string $profile): array {
    $folder = $this->fs->profileFolder($tenant, $entity, $identity, $index, $profile);
    return Storage::disk($this->scope)->files($folder);
  }

  /**
   * Update image index, replace one with another and vice versa.
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param int $source Source index
   * @param int $target Target index
   */
  public function switchIndex(string $tenant, string $entity, string $identity, int $source, int $target): void {
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