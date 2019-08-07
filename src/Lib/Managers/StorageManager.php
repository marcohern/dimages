<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\Fs;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

class StorageManager {
  protected $scope = DimageConstants::SCOPE;
  protected $fs;

  public function __construct() {
    $this->fs = Fs::getInstance();
  }

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
    return Storage::disk($this->scope)->get($dimage->toFilePath());
  }

  public function put(DimageFile $dimage, string &$content) {
    Storage::disk($this->scope)->put($dimage->toFilePath(), $content);
  }

  public function destroy(DimageFile $dimage) {
    $disk = Storage::disk($this->scope);
    if ($disk->exists($dimage->toFilePath())) $disk->delete($dimage->toFilePath());
    else throw new DimageNotFoundException("Dimage not found");
  }

  public function deleteIndex(string $tenant, string $entity, string $identity, int $index) {
    $disk = Storage::disk($this->scope);
    $folder = $this->fs->indexFolder($tenant, $entity, $identity, $index);
    $sourceFolder = $this->fs->identityFolder($tenant, $entity, $identity);
    $files = $disk->files($sourceFolder);
    foreach ($files as $file) {
      $dimage = DimageFile::fromFilePath($file);
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
    $files = DimageFunctions::toFilePaths($dimages);
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

  public function storeIdentity(string $tenant, string $entity, string $identity, UploadedFile $upload) {
    $sequencer = new DimageSequencer($entity, $identity, $tenant);
    $dimage = new DimageFile(
      $entity, $identity,
      $sequencer->next(),
      $upload->getClientOriginalExtension(),
      '', '', $tenant
    );
    $this->store($dimage, $upload);
    return $dimage;
  }

  public function updateIdentity(string $tenant, string $entity, string $identity, int $index, UploadedFile $upload) {
    $files = $this->sources($tenant, $entity, $identity);
    foreach ($files as $file) {
      $old = DimageFile::fromFilePath($file);
      if ($old->index === $index) {
        $new = clone $old;
        $new->ext = $upload->getClientOriginalExtension();
        if ($old->ext != $new->ext)
          $this->deleteIndex($tenant, $entity, $identity, $index);
        $this->store($new, $upload);
        return $new;
      }
    }
    throw new DimageNotFoundException("Image not found: $tenant/$entity/$identity/$index");
  }

  public function stageIdentity(string $tenant, string $session, UploadedFile $upload) {
    return $this->storeIdentity($tenant, '_tmp', $session, $upload);
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
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
  }

  public function identities(string $tenant, string $entity) : array {
    $folder = $this->fs->entityFolder($tenant, $entity);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
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
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
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
      $dimage = DimageFile::fromFilePath($file);
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
      $tmpDimage = new DimageFile($entity, $identity, $source, 'tmpx', '', '', $tenant);
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

  public function normalize(string $tenant, string $entity, string $identity) : void {
    $files = $this->sources($tenant, $entity, $identity);
    foreach ($files as $i => $file) {
      $dimage = DimageFile::fromFilePath($file);
      if ($dimage->index != $i)
        $this->switchIndex($tenant, $entity, $identity, $dimage->index, $i);
    }
    
    $sequencer = new DimageSequencer($tenant, $entity, $identity);
    $sequencer->put(count($files));
  }
}