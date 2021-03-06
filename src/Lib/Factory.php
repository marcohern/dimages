<?php

namespace Marcohern\Dimages\Lib;

use Illuminate\Support\Facades\Storage;

class Factory {
  protected $fs;
  protected $scope = Constants::SCOPE;

  public function __construct(Fs $fs) {
    $this->fs = $fs;
  }

  public function dimageFile(
    string $identity, string $ext, int $index = 0,
    string $entity = Constants::DFENTITY,
    string $profile = '', string $density = '',
    string $tenant=Constants::DFTENANT) : DimageFile 
  {
    return new DimageFile($this->fs, $identity, $ext, $index, $entity, $profile, $density, $tenant);
  }

  public function dimageFileFromPath(string $filepath)
  {
    return DimageFile::fromFilePath($this->fs, $filepath);
  }

  public function sequencer(
    string $identity, 
    string $entity = Constants::DFENTITY,
    string $tenant = Constants::DFTENANT
  ) {
    return new Sequencer($this->fs, $identity, $entity, $tenant);
  }

  public function settings(string $tenant) : Settings 
  {
    return new Settings(
      $this->fs, $tenant,
      config('dimages.densities'), config('dimages.profiles')
    );
  }

  public function loadSettings(string $tenant) : Settings
  {
    $disk = Storage::disk($this->scope);
    $file = $this->fs->settingsPath($tenant);
    if ($disk->exists($file)) {
      return unserialize($disk->get($file));
    } else {
      return new Settings(
        $this->fs, $tenant,
        config('dimages.densities'),
        config('dimages.profiles')
      );
    }
  }
}