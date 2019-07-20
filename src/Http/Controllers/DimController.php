<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimagesException;

use Marcohern\Dimages\Lib\DimageManager;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Http\Requests\UploadDimageRequest;

class DimController extends Controller
{
  protected $dimages;

  public function __construct(DimageManager $dimages) {
    $this->dimages = $dimages;
  }

  public function original($entity, $identity, $index=0) {
    $dimage = $this->dimages->source($entity, $identity, $index);
    $content = $this->dimages->content($dimage);
    $image = IImage::make($content);
    return $image->response($dimage->ext);
  }

  public function full($entity, $identity, $profile, $density, $index=0) {
    $dimage = $this->dimages->get($entity, $identity, $profile, $density, $index);
    return IImage::make($dimage->toFullPathFileName())->response($dimage->ext);
  }

  public function status() {
    return ['success' => true];
  }

  public function store(UploadDimageRequest $request, $entity, $identity) {
    $dimage = $this->dimages->storeIdentity($entity, $identity, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function destroy($entity, $identity) {
    $this->dimages->deleteIdentity($entity, $identity);
  }
}