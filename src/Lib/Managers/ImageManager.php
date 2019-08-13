<?php


namespace Marcohern\Dimages\Lib\Managers;

use Intervention\Image\ImageManagerStatic as IImage;

use Marcohern\Dimages\Lib\Files\DimageFile;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Lib\Lockable;
use Marcohern\Dimages\Lib\Factory;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

class ImageManager {

  use Lockable;

  protected $sm;
  protected $fs;

  public function __construct(StorageManager $sm, Factory $factory) {
    $this->sm = $sm;
    $this->factory = $factory;
  }

  public function sources($tenant, $entity, $identity) {
    $files = $this->sm->sources($tenant, $entity, $identity);
    $dimages = [];
    foreach ($files as $file) {
      $dimages[] = $this->factory->dimageFileFromPath($file);
    }
    return $dimages;
  }

  public function source(string $tenant, string $entity, string $identity, int $index=0):DimageFile {
    $files = $this->sm->sources($tenant, $entity, $identity);
    foreach ($files as $file) {
      $source = $this->factory->dimageFileFromPath($file);
      if ($source->index === $index) {
        return $source;
      }
    }
    throw new DimageNotFoundException("Source Image Not found: $tenant/$entity/$identity/$index", 0xd9745b9923);
  }

  protected function derivativeOrSource(
    string $tenant, string $entity, string $identity,
    string $profile, string $density, int $index=0
  ) : DimageFile {
    $files = $this->sm->derivatives($tenant, $entity, $identity, $index, $profile);
    foreach ($files as $file) {
      $target = $this->factory->dimageFileFromPath($file);
      if ($target->density === $density) {
        return $target;
      }
    }

    $files = $this->sm->sources($tenant, $entity, $identity);
    foreach ($files as $file) {
      $source = $this->factory->dimageFileFromPath($file);
      if ($source->index === $index) {
        return $source;
      }
    }

    throw new DimageNotFoundException("Dimage Not found: $tenant/$entity/$identity/$index", 0xd9745b9923);
  }

  protected function generate(DimageFile $source, $profile, $density) {
    if (!$this->sm->exists($source))
      throw new DimageNotFoundException("Source Not found", 0xd9745b9923);
    if (!$source->isSource())
      throw new DimageOperationInvalidException("Image must be source", 0xd9745b9923);
    
    $sourceContent = $this->sm->content($source);
    $target = clone $source;
    $target->profile = $profile;
    $target->density = $density;
    $p = config("dimages.profiles.$profile");
    $d = config("dimages.densities.$density");
    if (!$p) throw new DimageOperationInvalidException("Profile $profile invalid", 0xd9745b9921);
    if (!$d) throw new DimageOperationInvalidException("Density $density invalid", 0xd9745b9922);
    $w = intval($p[0]*$d);
    $h = intval($p[1]*$d);
    $targetContent = (string) IImage::make($sourceContent)->fit($w, $h)->encode($target->ext);
    $this->sm->put($target, $targetContent);
    return $target;
  }

  public function get(
    string $tenant, string $entity, string $identity,
    string $profile, string $density, int $index=0) : DimageFile
  {
    $dimage = $this->derivativeOrSource($tenant, $entity, $identity, $profile, $density, $index);
    if ($dimage->isDerived()) return $dimage;

    $source = $dimage;
    $derived = clone $dimage;
    $derived->profile = $profile;
    $derived->density = $density;
    
    $this->createLocksFolderIfNotExists();
    $this->openlock($source);
    if ($this->lock()) {
      if (!$this->sm->exists($derived)) $this->generate($source, $profile, $density);
      $this->unlock();
    }
    $this->closelock();

    return $derived;
  }

  public function session() {
    $template = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
    $tlen = strlen($template);
    $string = '';
    for($i=0;$i<128;$i++) {
      $n = rand(0,$tlen-1);
      $string .= $template[$n];
    }
    $date = date("Y-m-d H:i:s");
    $number = rand(10000, 99999);
    $md5 = md5("$date/$string/$number");
    return substr($md5,0,16);
  }
}