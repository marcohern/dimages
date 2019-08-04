<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\DimageFolders;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageSequencer;

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
    return Storage::disk($this->scope)->get($dimage->toFilePath());
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
    $source = DimageFolders::staging($tenant, $session);
    $target = DimageFolders::sources($tenant, $targetEntity, $targetIdentity);
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

  public function stageIdentity(string $tenant, string $session, UploadedFile $upload) {
    return $this->storeIdentity($tenant, '_tmp', $session, $upload);
  }

  public function deleteIdentity(string $tenant,string $entity,string $identity):void {
    $folder = DimageFolders::sources($tenant, $entity, $identity);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  public function deleteStaging(string $tenant, string $session) : void {
    $folder = DimageFolders::staging($tenant, $session);
    Storage::disk($this->scope)->deleteDirectory($folder);
  }

  public function deleteStagingForTenants(array $tenants) : void {
    foreach ($tenants as $tenant) {
      $folder = DimageFolders::stagingFolder($tenant);
      Storage::disk($this->scope)->deleteDirectory($folder);
    }
  }

  public function tenants() : array {
    return Storage::disk($this->scope)->directories("/");
  }

  public function entities(string $tenant) : array {
    $folder = DimageFolders::entities($tenant);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
  }

  public function identities(string $tenant, string $entity) : array {
    $folder = DimageFolders::identities($tenant, $entity);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
  }

  public function sources(string $tenant, string $entity, string $identity) : array {
    $folder = DimageFolders::sources($tenant, $entity, $identity);
    return Storage::disk($this->scope)->files($folder);
  }

  public function profiles(string $tenant, string $entity, string $identity, int $index) : array {
    $folder = DimageFolders::profiles($tenant, $entity, $identity, $index);
    $subfolders = Storage::disk($this->scope)->directories($folder);
    return DimageFunctions::suffix($subfolders, strlen($folder)+1);
  }

  public function derivatives(string $tenant, string $entity, string $identity, int $index, string $profile) : array {
    $folder = DimageFolders::derivatives($tenant, $entity, $identity, $index, $profile);
    return Storage::disk($this->scope)->files($folder);
  }
}