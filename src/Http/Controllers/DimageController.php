<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimagesException;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageManager;
use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\Managers\ImageManager;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Http\Requests\UploadDimageRequest;

class DimageController extends Controller {
  protected $im;
  protected $sm;

  public function __construct(StorageManager $sm, ImageManager $im) {
    $this->im = $im;
    $this->sm = $sm;
  }

  public function status() {
    return [
      'success' => true,
      'xFile' => Dimage::xFile(),
      'xUrl' => Dimage::xUrl()
    ];
  }

  public function session() {
    $template = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
    $tlen = strlen($template);
    $string = '';
    for($i=0;$i<128;$i++) {
      $n = rand(0,$tlen-1);
      $string .= $template[$n];
    }
    $date = date("Y-m-d H:i:s");
    $number = rand(10000, 99999);
    $md5 = md5("$date/$string/$number");
    $session = substr($md5,0,16);
    return [
      'session' => $session
    ];
  }

  public function tenants() {
    return $this->sm->tenants();
  }

  public function entities(string $tenant) {
    return $this->sm->entities($tenant);
  }

  public function identities(string $tenant, string $entity) {
    return $this->sm->identities($tenant, $entity);
  }

  public function sources(string $tenant, string $entity, string $identity) {
    return $this->im->sources($tenant, $entity, $identity);
  }

  public function source(string $tenant, string $entity, string $identity, int $index = 0) {
    return $this->im->get($tenant, $entity, $identity, $index);
  }

  public function store(UploadDimageRequest $request, string $tenant, string $entity, string $identity) {
    $dimage = $this->sm->storeIdentity($tenant, $entity, $identity, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function stage(UploadDimageRequest $request, string $tenant, string $session) {
    $dimage = $this->sm->stageIdentity($tenant, $session, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function attach(string $tenant, string $session, string $entity, string $identity) {
    $dimage = $this->sm->attach($tenant, $session, $entity, $identity);
  }

  public function switch(string $tenant, string $entity, string $identity, int $source, int $target) {
    $this->sm->switchIndex($tenant, $entity, $identity, $source, $target);
  }

  public function derive(string $tenant, string $entity, string $identity, string $profile, string $density, int $index = 0) {
    $dimage = $this->im->get($tenant, $entity, $identity, $profile, $density, $index);
    $content = $this->sm->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function normalize(string $tenant, string $entity, string $identity) {
    $this->sm->normalize($tenant, $entity, $identity);
  }

  public function update(UploadDimageRequest $request, string $tenant, string $entity, string $identity, int $index) {
    $this->sm->updateIdentity($tenant, $entity, $identity, $index, $request->image);
  }
}