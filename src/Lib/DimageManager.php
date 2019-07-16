<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as IImage;

use Marcohern\Dimages\Lib\DimageName;
use Marcohern\Dimages\Lib\DimageSequencer;

use Marcohern\Dimages\Exceptions\DimagesException;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;

class DimageManager {

  private $scope;
  public function __construct() {
    $this->scope = DimageConstants::FSSCOPE;
  }

  public function url(DimageName $dimage) : string {
    return DimageConstants::DIMROUTE.'/'.$dimage->toUrl();
  }

  public function diskUrl(DimageName $dimage) : string {
    $disk = Storage::disk($this->scope);
    return $disk->url($dimage->toIdentityPathFileName());
  }

  public function file(DimageName $dimage) : string {
    $disk = Storage::disk($this->scope);
    $storage = storage_path();
    return $storage
      .'/'.DimageConstants::FSPATH
      .'/'.DimageConstants::IMAGESUBDIR
      .'/'.$dimage->toIdentityPathFileName();
  }

  public function store(string $entity, string $identity, UploadedFile $upload) : DimageName {
    $sequencer = new DimageSequencer($entity, $identity);
    $disk = Storage::disk($this->scope);
    $index = $sequencer->next();

    $dimage = new DimageName;
    $dimage->entity = $entity;
    $dimage->identity = $identity;
    $dimage->index = $index;
    $dimage->ext = $upload->getClientOriginalExtension();
    $disk->putFileAs($dimage->toFullPath(), $upload, $dimage->toFileName());
    return $dimage;
  }

  public function getSourceName($entity, $identity, $index=0) : DimageName {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
    $files = $disk->files($dir);
    foreach ($files as $file) {
      $dimage = DimageName::fromFilePath($file);
      if ($dimage->index == $index) { return $dimage; }
    }
    throw new DimageNotFoundException("Image not found:$entity/$identity/$index", 0xd9745b991e);
  }

  public function getName($entity, $identity, $profile, $density, $index=0) : DimageName {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
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
    throw new DimageNotFoundException("Image not found:$entity/$identity/$profile/$density/$index", 0xd9745b991f);
  }

  public function exists(DimageName $dimage) {
    $disk = Storage::disk($this->scope);
    $file = $dimage->toFullPathFileName();
    return $disk->exists($file);
  }

  public function list($entity, $identity) {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
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
    $prefix = DimageFunctions::imagesFolder();
    $plen = strlen($prefix) + 1;
    $dirs = $disk->directories($prefix);
    foreach ($dirs as $k => $dir) {
      $dirs[$k] = substr($dir, $plen);
    }
    if (empty($dirs)) {
      $disk->deleteDirectory($prefix);
    }
    return $dirs;
  }

  public function identities($entity) {
    $disk = Storage::disk($this->scope);
    $prefix = DimageFunctions::entityFolder($entity);
    $plen = strlen($prefix) + 1;
    $dirs = $disk->directories($prefix);
    foreach ($dirs as $k => $dir) {
      $dirs[$k] = substr($dir, $plen);
    }
    if (empty($dirs)) {
      $disk->deleteDirectory($prefix);
    }
    return $dirs;
  }

  public function switchIndex($entity, $identity, $source, $target) {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
    $files = $disk->files($dir);
    $dimages = [];
    $dsource = null;
    $dtarget = null;
    foreach ($files as $file) {
      $dimage = DimageName::fromFilePath($file);
      if ($dimage->isSource()) {
        if      ($dimage->index == $source) $dsource = $dimage;
        else if ($dimage->index == $target) $dtarget = $dimage;
      } else {
        if ($dimage->index == $source || $dimage->index == $target)
          $disk->delete($dimage->toFullPathFileName());
      }
      //if ($dsource && $dtarget) break;
    }
    if (empty($dsource)) throw new DimageNotFoundException("Source index not found: $source",0x0);
    if (empty($dtarget)) {
      $disk->move("$dir/".$dsource->toFileName(), "$dir/".$dtarget->toFileName());
    } else {
      $disk->move("$dir/".$dtarget->toFileName(), "$dir/tmp-".$dtarget->toFileName());
      $disk->move("$dir/".$dsource->toFileName(), "$dir/".$dtarget->toFileName());
      $disk->move("$dir/tmp-".$dtarget->toFileName(), "$dir/".$dsource->toFileName());
    }
    $dsource->index = $target;
    $dtarget->index = $source;
    return ['newSource' => $dsource, 'newTarget' => $dtarget ];
  }

  public function destroy($entity, $identity) {
    $disk = Storage::disk($this->scope);
    $sequencer = new DimageSequencer($entity, $identity);
    $dir = DimageFunctions::identityFolder($entity,$identity);
    if ($disk->exists($dir)) {
      $sequencer->drop();
      $disk->deleteDirectory($dir);
    } else {
      throw new DimageNotFoundException("Dir not found: $dir");
    }
  }

  public function getSourceAndDerivedFiles($entity, $identity, $index) {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
    
  }

  public function destroySingle($entity, $identity, $index) {
    $disk = Storage::disk($this->scope);
    $dir = DimageFunctions::identityFolder($entity,$identity);
  }
}