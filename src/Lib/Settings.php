<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Fs;

class Settings {

  protected $fs;
  protected $densities;
  protected $profiles;

  protected $tenant;
  
  public function __construct(Fs $fs) {
    $this->fs = $fs;
    $this->densities = config('dimages.densities');
    $this->profiles = config('dimages.profiles');
  }

  public function setTenant(string $tenant) {
    $this->tenant = $tenant;
  }

  public function save() {
    $serialized = serialize($this);
    $file = $this->fs->settingsPath($this->tenant);
    Storage::disk('dimages')->put($file, $serialized);
  }
}