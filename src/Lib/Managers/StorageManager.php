<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\Fs;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

class StorageManager extends DiskStorageManager {

  public function storeIdentity(string $tenant, string $entity, string $identity, UploadedFile $upload) {
    $sequencer = $this->factory->sequencer($identity, $entity, $tenant);
    $dimage = $this->factory->dimageFile(
      $identity, $upload->getClientOriginalExtension(),
      $sequencer->next(), $entity, '', '', $tenant
    );
    $this->store($dimage, $upload);
    return $dimage;
  }

  public function updateIdentity(string $tenant, string $entity, string $identity, int $index, UploadedFile $upload) {
    $files = $this->sources($tenant, $entity, $identity);
    foreach ($files as $file) {
      $old = $this->factory->dimageFileFromPath($file);
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

  public function normalize(string $tenant, string $entity, string $identity) : void {
    $files = $this->sources($tenant, $entity, $identity);
    foreach ($files as $i => $file) {
      $dimage = $this->factory->dimageFileFromPath($file);
      if ($dimage->index != $i)
        $this->switchIndex($tenant, $entity, $identity, $dimage->index, $i);
    }
    
    $sequencer = $this->factory->sequencer($identity, $entity, $tenant);
    $sequencer->put(count($files));
  }
}