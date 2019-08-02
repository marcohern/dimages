<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Lib\DimageName;

class StorageManager {
  protected $scope = 'dimages';
  protected $tenant = '_global';

  public function setScope($scope) {
    $this->scope = $scope;
  }

  public function setTenant($tenant) {
    $this->tenant = $tenant;
  }

  public function tenant($suffix) {
    return "{$this->tenant}/$suffix";
  }

  public function url(DimageName $dimage) : string {
    return Storage::disk($this->scope)->url($this->tenant($dimage->toIdentityPathFileName()));
  }

  public function exists(DimageName $dimage) : string {
    $r = Storage::disk($this->scope)->exists($this->tenant($dimage->toIdentityPathFileName()));
    return $r;
  }

  public function content(DimageName $dimage) : string {
    return Storage::disk($this->scope)->exists($this->tenant($dimage->toIdentityPathFileName()));
  }
}