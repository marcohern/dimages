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

  public function source($entity, $identity, $index=0) {
    $dimage = $this->dimages->source($entity, $identity, $index);
    $content = $this->dimages->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function derive($entity, $identity, $profile, $density, $index=0) {
    $dimage = $this->dimages->get($entity, $identity, $profile, $density, $index);
    $content = $this->dimages->content($dimage);
    return IImage::make($content)->response($dimage->ext);
  }

  public function store(UploadDimageRequest $request, $entity, $identity) {
    $dimage = $this->dimages->storeIdentity($entity, $identity, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function destroy($entity, $identity, $index = null) {
    if (is_null($index)) $this->dimages->deleteIdentity($entity, $identity);
    else $this->dimages->deleteIndex($entity, $identity, $index);
  }

  public function switch($entity, $identity, $source, $target) {
    $this->dimages->switchIndex($entity, $identity, $source, $target);
  }

  public function normalize($entity, $identity) {
    $this->dimages->normalize($entity, $idenitty);
  }
}