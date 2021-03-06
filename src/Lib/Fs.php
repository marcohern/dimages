<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Constants;
use Marcohern\Dimages\Lib\Functions;
class Fs {

  protected $prefix = '';

  public static function clearInstance() {
    unset(self::$instance);
  }

  public function setRoot(string $prefix) {
    $this->prefix = $prefix;
  }

  public function root(string $suffix): string {
    if (empty($this->prefix)) {
      if (empty($suffix)) return '';
      else return $suffix;
    }
    else if (empty($suffix)) return $this->prefix;
    return "{$this->prefix}/$suffix";
  }

  public function rootFolder(): string {
    return $this->root('');
  }

  public function tenantFolder(string $tenant): string {
    return $this->root($tenant);
  }

  public function entityFolder(string $tenant, string $entity): string {
    return $this->root("$tenant/$entity");
  }

  public function identityFolder(string $tenant, string $entity, string $identity): string {
    return $this->root("$tenant/$entity/$identity");
  }

  public function indexFolder(string $tenant, string $entity, string $identity, int $index): string {
    $pindex = Functions::padIndex($index);
    return $this->root("$tenant/$entity/$identity/$pindex");
  }

  public function profileFolder(string $tenant, string $entity, string $identity, int $index, string $profile): string {
    $pindex = Functions::padIndex($index);
    return $this->root("$tenant/$entity/$identity/$pindex/$profile");
  }

  public function stagingFolder(string $tenant) {
    return $this->root("$tenant/".Constants::STAGING);
  }

  public function stagingSessionFolder(string $tenant, string $session) {
    return $this->root("$tenant/".Constants::STAGING."/$session");
  }

  public function sourcePath(string $tenant, string $entity, string $identity, int $index, string $ext): string {
    $pindex = Functions::padIndex($index);
    return $this->root("$tenant/$entity/$identity/$pindex.$ext");
  }

  public function derivedPath(
    string $tenant, string $entity, string $identity,
    int $index, string $profile, string $density, string $ext): string 
  {
    $pindex = Functions::padIndex($index);
    return $this->root("$tenant/$entity/$identity/$pindex/$profile/$density.$ext");
  }

  public function sourceFile(int $index, string $ext): string {
    $pindex = Functions::padIndex($index);
    return "$pindex.$ext";
  }

  public function derivedFile(string $profile, string $ext): string {
    return "$profile.$ext";
  }

  public function sequencePath(string $tenant, string $entity, string $identity): string {
    return $this->root("$tenant/".Constants::SEQUENCE."/$entity.$identity.id");
  }

  public function settingsPath(string $tenant): string {
    return $this->root("$tenant/".Constants::SETTINGS);
  }


}