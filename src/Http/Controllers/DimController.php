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
use Marcohern\Dimages\Http\Requests\UploadDimageRequest;

class DimController extends Controller
{
  protected $dimages;

  public function __construct(DimageManager $dimages) {
    $this->dimages = $dimages;
  }

  public function status() {
    return [
      'success' => true,
      'xFileName' => Dimage::xFileName(),
      'xUrl' => Dimage::xUrl()
    ];
  }

  public function index(string $entity, string $identity) {
    return $this->dimages->dimages($entity, $identity);
  }

  public function source(string $entity, string $identity, int $index=0) {
    $dimage = $this->dimages->source($entity, $identity, $index);
    $content = $this->dimages->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function derive(string $entity, string $identity, string $profile, string $density, int $index=0) {
    $dimage = $this->dimages->get($entity, $identity, $profile, $density, $index);
    $content = $this->dimages->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function store(UploadDimageRequest $request, string $entity, string $identity) {
    $dimage = $this->dimages->storeIdentity($entity, $identity, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function update(UploadDimageRequest $request, string $entity, string $identity, int $index) {
    $this->dimages->update($entity, $identity, $index, $request->image);
  }

  public function sources(string $entity, string $identity) {
    return $this->dimages->sources($entity, $identity);
  }

  public function derivatives(string $entity, string $identity) {
    return $this->dimages->derivatives($entity, $identity);
  }

  public function destroy(string $entity, string $identity, $index = null) {
    if (is_null($index)) $this->dimages->deleteIdentity($entity, $identity);
    else $this->dimages->deleteIndex($entity, $identity, $index);
  }

  public function switch(string $entity, string $identity, int $source, int $target) {
    $this->dimages->switchIndex($entity, $identity, $source, $target);
  }

  public function normalize(string $entity, string $identity) {
    $this->dimages->normalize($entity, $identity);
  }

  public function dimages(string $entity, string $identity) {
    $this->dimages->dimages($entity, $identity);
  }

  public function entities(Request $request) {
    $entities = $this->dimages->entities();
    return $entities;
  }

  public function identities(Request $request, string $entity) {
    $identities = $this->dimages->identities($entity);
    return $identities;
  }
}