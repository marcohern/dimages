<?php

namespace Marcohern\Dimages\Lib\Dimages;

use Illuminate\Support\Facades\Storage;
use Marcohern\Dimages\Lib\Dimages\DimageName;
use Marcohern\Dimages\Lib\Dimages\DimageSequencer;
use Marcohern\Dimages\Exceptions\ImageException;

class DimageManager {

  private $scope;
  public function __construct() {
    $this->scope = DimageConstants::FSSCOPE;
  }

  public function url($dimage) {
    return DimageConstants::IMAGEROUTE.'/'.$dimage->toUrl();
  }

  public function store($entity, $identity, $upload) : DimageName {
    $sequencer = new DimageSequencer($this->scope);
    $disk = Storage::disk($this->scope);
    $index = $sequencer->nextFrom("$entity.$identity.id");

    $dimage = new DimageName;
    $dimage->entity = $entity;
    $dimage->identity = $identity;
    $dimage->index = $index;
    $dimage->ext = $upload->getClientOriginalExtension();
    $disk->putFileAs(DimageConstants::IMAGESUBDIR.'/'.$dimage->getPath(), $upload, $dimage->getName());
    return $dimage;
  }

  public function viewMain($entity, $identity, $index=0) : DimageName {
    $disk = Storage::disk($this->scope);
    $dir = DimageConstants::IMAGESUBDIR.'/'.DimageFunctions::imgFolder($entity,$identity);
    $files = $disk->files($dir);
    foreach ($files as $file) {
      $dimage = DimageName::fromFilePath($file);
      if ($dimage->index == $index) { return $dimage; }
    }
    throw new ImageException("Image not found:$entity/$identity/$index", 0xd9745b991e);
  }

  public function viewExact($entity, $identity, $profile, $density, $index=0) {
    $disk = Storage::disk($this->scope);
    $dir = DimageConstants::IMAGESUBDIR.'/'.DimageFunctions::imgFolder($entity,$identity);
    $files = $disk->files($dir);
    foreach ($files as $file) {
      $dimage = DimageName::fromFilePath($file);
      if ($dimage->index == $index && $dimage->profile==$profile && $dimage->density==$density) {
        return $dimage;
      } else if ($dimage->index == $index) {
        $dimage->profile = $profile;
        $dimage->density = $density;
        return $dimage;
      }
    }
    throw new ImageException("Image not found:$entity/$identity/$profile/$density/$index");
  }

  public function list($entity, $identity) {
    $disk = Storage::disk($this->scope);
    $dir = DimageConstants::IMAGESUBDIR.'/'.DimageFunctions::imgFolder($entity,$identity);
    $files = $disk->files($dir);
    $dimages = [];
    foreach ($files as $file) {
      $dimage = DimageName::fromFilePath("$dir/$file");
      if ($dimage->isSource()) $dimages[] = $dimage;
    }
    return $dimages;
  }

  public function entities() {
    $disk = Storage::disk($this->scope);
    return $disk->directories(DimageConstants::IMAGESUBDIR);
  }

  public function identities($entity) {
    $disk = Storage::disk($this->scope);
    return $disk->directories(DimageConstants::IMAGESUBDIR."/$entity");
  }

  public function update_index($entity, $identity, $source, $dest) {
    $disk = Storage::disk($this->scope);

  }
}