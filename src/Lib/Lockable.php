<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Support\Facades\File;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFile;

trait Lockable {

  protected static $storage = DimageConstants::LOCKS;

  protected $lock;
  protected $lockfile;

  protected function createLocksFolderIfNotExists() {
    $path = storage_path(self::$storage);
    if (!File::exists($path)) File::makeDirectory($path);
  }
  
  protected function deleteLocks() {
    $path = storage_path(self::$storage);
    $files = File::files($path);
    $cnt = count($files);
    $this->info("Deleted $cnt files!");
    File::delete($files);
  }

  protected function openlock(DimageFile $dimage) {
    $md5 = md5("{$dimage->tenant}.{$dimage->entity}.{$dimage->identity}.{$dimage->index}");
    $fname = "$md5.lock";
    $this->lockfile = storage_path(self::$storage."/$fname");
    $this->lock = fopen($this->lockfile, "a+");
  }

  protected function lock() {
    return flock($this->lock, LOCK_EX);
  }

  protected function unlock() {
    flock($this->lock, LOCK_UN);
  }

  protected function closelock() {
    fclose($this->lock);
  }
}