<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Fs;
use Illuminate\Support\Facades\Storage;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;

class Settings {

  protected $fs;
  protected $densities;
  protected $profiles;
  protected $tenant;

  public function getDensities() { return $this->densities; }
  public function getProfiles() { return $this->profiles; }

  public function density(string $density): float {
    if (array_key_exists($density, $this->densities)) 
      return $this->densities[$density];
    throw new DimageOperationInvalidException("Density $density invalid", 0xd9745b9921);
  }

  public function profile(string $profile): array {
    if (array_key_exists($profile, $this->profiles)) 
      return $this->profiles[$profile];
    throw new DimageOperationInvalidException("Profile $profile invalid", 0xd9745b9922);
  }

  public function setDensity(string $density, float $value): void {
    $this->densities[$density] = $value;
  }

  public function setProfile(string $profile, int $width, int $height): void {
    $this->profiles[$profile] = [$width, $height];
  }
  
  public function __construct(Fs $fs, string $tenant, array $densities, array $profiles) {
    $this->fs = $fs;
    $this->tenant = $tenant;
    $this->densities = $densities;
    $this->profiles = $profiles;
  }

  public function save() {
    $serialized = serialize($this);
    $file = $this->fs->settingsPath($this->tenant);
    Storage::disk('dimages')->put($file, $serialized);
  }
}