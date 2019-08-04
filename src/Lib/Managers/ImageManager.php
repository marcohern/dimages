<?php


namespace Marcohern\Dimages\Lib\Managers;

use Intervention\Image\ImageManagerStatic as IImage;

use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Lib\DimageFolders;
use Marcohern\Dimages\Lib\Files\DimageFile;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

class ImageManager {
  protected $sm;
  protected $tenant = '_global';

  public function __construct(StorageManager $sm) {
    $this->sm = $sm;
  }

  public function sources($tenant, $entity, $identity) {
    $files = $this->sm->sources($tenant, $entity, $identity);
    $dimages = [];
    foreach ($files as $file) {
      $dimages[] = DimageFile::fromFilePath($file);
    }
    return $dimages;
  }

  public function source(string $tenant, string $entity, string $identity, int $index=0):string {
    $files = $this->sm->sources($tenant, $entity, $identity);
    foreach ($files as $file) {
      $source = DimageFile::fromFilePath($file);
      if ($source->index === $index) {
        return $this->sm->content($source);
      }
    }
    throw new DimageNotFoundException("Source Image Not found: $tenant/$entity/$identity/$index", 0xd9745b9923);
  }

  public function get(
    string $tenant, string $entity, string $identity,
    string $profile, string $density, int $index=0) : string
  {
    $files = $this->sm->derivatives($tenant, $entity, $identity, $index, $profile);
    foreach ($files as $file) {
      $target = DimageFile::fromFilePath($file);
      if ($target->density === $density) {
        return $this->sm->content($target);
      }
    }
    
    $sourceContent = $this->source($tenant, $entity, $identity, $index);
    $target = clone $source;
    $target->profile = $profile;
    $target->density = $density;
    $p = config("dimages.profiles.$profile");
    $d = config("dimages.densities.$density");
    if (!$p) throw new DimageOperationInvalidException("Profile $profile invalid", 0xd9745b9921);
    if (!$d) throw new DimageOperationInvalidException("Density $density invalid", 0xd9745b9922);
    $w = $p[0]*$d;
    $h = $p[1]*$d;
    $targetContent = (string) IImage::make($sourceContent)->fit($w, $h)->encode($target->ext);
    $this->put($target, $targetContent);
    return $targetContent;
  }
}