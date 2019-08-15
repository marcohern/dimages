<?php

namespace Marcohern\Dimages\Lib\Managers;

use Intervention\Image\ImageManagerStatic as IImage;

use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Lib\DimageFile;
use Marcohern\Dimages\Lib\Lockable;
use Marcohern\Dimages\Lib\Factory;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

/**
 * Loads and Manipulates images
 */
class ImageManager {

  use Lockable;

  /**
   * Storage manager
   */
  protected $sm;

  /**
   * Factory
   */
  protected $factory;

  /**
   * Constructor
   * @param Marcohern\Dimages\Lib\Managers\StorageManager $sm Storage manager
   * @param Marcohern\Dimages\Lib\Factory $factory Factory
   */
  public function __construct(StorageManager $sm, Factory $factory) {
    $this->sm = $sm;
    $this->factory = $factory;
  }

  /**
   * Return sources as array of DimageFile
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @return array list of DimageFile
   */
  public function sources(string $tenant, string $entity, string $identity): array {
    $files = $this->sm->sources($tenant, $entity, $identity);
    $dimages = [];
    foreach ($files as $file) {
      $dimages[] = $this->factory->dimageFileFromPath($file);
    }
    return $dimages;
  }

  /**
   * Return the source DimageFile
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param int $index Index
   * @return Marcohern\Dimages\Lib\DimageFile DimageFile found
   */
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

  /**
   * Find and return the specified derived image. If not found, then find and return the source.
   * 
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param string $profile Profile
   * @param string $density Density
   * @param int $index Index
   * @return Marcohern\Dimages\Lib\DimageFile DimageFile found
   */
  protected function derivativeOrSource(
    string $tenant, string $entity, string $identity,
    string $profile, string $density, int $index=0
  ): DimageFile {
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

  /**
   * Generate a derived dimage file from a source.
   * @param Marcohern\Dimages\Lib\DimageFile $source Source image
   * @param string $profile Desired profile
   * @param string $density Desired density
   * @return Marcohern\Dimages\Lib\DimageFile Generated image
   */
  protected function generate(DimageFile $source, string $profile, string $density): DimageFile {
    if (!$this->sm->exists($source))
      throw new DimageNotFoundException("Source Not found", 0xd9745b9923);
    if (!$source->isSource())
      throw new DimageOperationInvalidException("Image must be source", 0xd9745b9923);
    
    $settings = $this->factory->loadSettings($source->tenant);
    $sourceContent = $this->sm->content($source);
    $target = clone $source;
    $target->profile = $profile;
    $target->density = $density;
    $p = $settings->profile($profile);
    $d = $settings->density($density);
    $w = intval($p[0]*$d);
    $h = intval($p[1]*$d);
    $targetContent = (string) IImage::make($sourceContent)->fit($w, $h)->encode($target->ext);
    $this->sm->put($target, $targetContent);
    return $target;
  }

  /**
   * Derive an image. If derivative is not found, then generate from source.
   * @param string $tenant Tenant
   * @param string $entity Entity
   * @param string $identity Identity
   * @param string $profile Profile
   * @param string $density Density
   * @param int $index Index
   * @return Marcohern\Dimages\Lib\DimageFile Derivative image file
   */
  public function get(
    string $tenant, string $entity, string $identity,
    string $profile, string $density, int $index=0): DimageFile
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
      if (!$this->sm->exists($derived))
        $this->generate($source, $profile, $density);
      $this->unlock();
    }
    $this->closelock();

    return $derived;
  }

  /**
   * Generate a random session id. Usefull for staging uploads.
   * @return string Session Id
   */
  public function session(): string {
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