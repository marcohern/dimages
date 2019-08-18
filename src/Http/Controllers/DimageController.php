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

  public function source(string $tenant, string $entity, string $identity, int $index = 0) {
    $dimage = $this->im->source($tenant, $entity, $identity, $index);
    $content = $this->sm->content($dimage);
    return IImage::make($content)->response($dimage->ext);
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

  public function derive(string $tenant, string $entity, string $identity, string $profile, string $density, int $index = 0) {
    $dimage = $this->im->get($tenant, $entity, $identity, $profile, $density, $index);
    $content = $this->sm->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function update(UploadDimageRequest $request, string $tenant, string $entity, string $identity, int $index) {
    $this->sm->updateIdentity($tenant, $entity, $identity, $index, $request->image);
  }

  public function destroyIndex(string $tenant, string $entity, string $identity, int $index) {
    $this->sm->deleteIndex($tenant, $entity, $identity, $index);
  }

  public function destroy(string $tenant, string $entity, string $identity) {
    $this->sm->deleteIdentity($tenant, $entity, $identity);
  }
}