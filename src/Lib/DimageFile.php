<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\Fs;

use Marcohern\Dimages\Exceptions\SourceInvalidException;

/**
 * Model for the image file name.
 */
class DimageFile {
  
  /**
   * Fs instance. helps figure out wherewhat the folders are.
   */
  protected $fs;

  /**
   * Image tenant or user.
   */
  public $tenant;

  /**
   * The image entity.
   */
  public $entity;

  /**
   * The image identity.
   */
  public $identity;

  /**
   * Image index.
   */
  public $index;

  /**
   * Image profile.
   */
  public $profile;

  /**
   * Image Density.
   */
  public $density;

  /**
   * Image extension.
   */
  public $ext;

  /**
   * Create a DimageFile instance from the file path.
   * @param Fs $fs File system access
   * @param string $filepath Image file path
   * @return Marcohern\Dimages\Lib\Files\DimageFile
   */
  public static function fromFilePath(Fs $fs, string $filepath):DimageFile {
    $m = null;
    $r = preg_match(Dimage::xFile(),$filepath, $m);
    if (!$r) {
      throw new SourceInvalidException("source invalid: $haystack.", 0xa996a53d53);
    }
    $m = (object)$m;
    
    return new DimageFile(
      $fs, $m->identity, $m->ext, 0+$m->index,
      $m->entity, $m->profile, $m->density, $m->tenant);
  }

  /**
   * Constructor
   * @param string $identity Identity
   * @param int $index Index
   * @param string $ext Extension
   * @param string $entity Entity
   * @param string $profile Profile
   * @param string $density Density
   * @param string $tenant Tenant or User
   */
  public function __construct(
    Fs $fs,
    string $identity, string $ext, int $index = 0, 
    string $entity = DimageConstants::DFENTITY,
    string $profile='', string $density='',
    string $tenant=DimageConstants::DFTENANT)
  {
    $this->entity = $entity;
    $this->identity = $identity;
    $this->index = $index;
    $this->ext = $ext;
    $this->profile = $profile;
    $this->density = $density;
    $this->tenant = $tenant;

    $this->fs = $fs;
  }

  /**
   * Determines if the current dimage is the source image
   * as opposed to the derived image.
   * @return bool true if it is the source, false otherwise.
   */
  public function isSource(): bool {
    if (empty($this->profile) && empty($this->density)) return true;
    return false;
  }

  /**
   * Determines if the current dimage is a derived image
   * as opposed to the source image.
   * @return bool true if it is the source, false otherwise.
   */
  public function isDerived(): bool {
    return !$this->isSource();
  }

  /**
   * Return the Source image of this image. If the image
   * is the source then it will just clone.
   * @return Marcohern\Dimages\Lib\Files\DimageFile The source image.
   */
  public function source(): DimageFile {
    return new DimageFile($this->fs,
      $this->identity, $this->index, $this->ext,
      $this->entity, '', '', $this->tenant);
  }

  /**
   * Return the file path to the image.
   * @return string file path to image, relative to storage root.
   */
  public function toFilePath(): string {
    if ($this->isSource())
      return $this->fs->sourcePath(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->ext);
    else
      return $this->fs->derivedPath(
        $this->tenant, $this->entity, $this->identity,
        $this->index, $this->profile, $this->density, $this->ext);
  }

  /**
   * Return the folder where the image file is stored.
   * @return string path to folder where file is in, relative to storage root.
   */
  public function toFolder(): string {
    if ($this->isSource())
      return $this->fs->identityFolder($this->tenant, $this->entity, $this->identity);
    else
      return $this->fs->profileFolder($this->tenant, $this->entity, $this->identity, $this->index, $this->profile);
  }

  /**
   * Return the file name of the image.
   * @return string Image file name.
   */
  public function toFileName(): string {
    if ($this->isSource())
      return $this->fs->sourceFile($this->index, $this->ext);
    else
      return $this->fs->derivedFile($this->density, $this->ext);
  }
}