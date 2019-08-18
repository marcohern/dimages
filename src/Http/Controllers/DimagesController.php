<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Dimages\Lib\Dimage;

use Marcohern\Dimages\Lib\Managers\ImageManager;
use Marcohern\Dimages\Lib\Managers\StorageManager;

class DimagesController extends Controller {
  protected $im;
  protected $sm;

  public function __construct(StorageManager $sm, ImageManager $im) {
    $this->im = $im;
    $this->sm = $sm;
  }

  public function status() {
    return [
      'success' => true,
      'xFile' => Dimage::xFile()
    ];
  }

  public function session() {
    return [
      'session' => $this->im->session()
    ];
  }

  public function switch(string $tenant, string $entity, string $identity, int $source, int $target) {
    $this->sm->switchIndex($tenant, $entity, $identity, $source, $target);
  }

  public function normalize(string $tenant, string $entity, string $identity) {
    $this->sm->normalize($tenant, $entity, $identity);
  }

  public function sources(string $tenant, string $entity, string $identity) {
    return $this->im->sources($tenant, $entity, $identity);
  }

  public function entities(string $tenant) {
    return $this->sm->entities($tenant);
  }

  public function identities(string $tenant, string $entity) {
    return $this->sm->identities($tenant, $entity);
  }

  public function tenants() {
    return $this->sm->tenants();
  }
}