<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\Dimager;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimagesException;

use Marcohern\Dimages\Lib\Dimages\DimageManager;
use Marcohern\Dimages\Lib\Dimages\DimageConstants;

class DimController extends Controller
{
  protected $dimages;

  public function __construct(DimageManager $dimages) {
    $this->dimages = $dimages;
  }

  public function original($entity, $identity, $index=0) {
    $dimage = $this->dimages->viewMain($entity, $identity, $index);
    $path = $this->dimages->file($dimage);
    $image = IImage::make($path);
    return $image->response($dimage->ext);
  }

  public function full($entity, $identity, $profile, $density, $index=0) {
    $dimage = $this->dimages->viewExact($entity, $identity, $profile, $density, $index);
    if ($this->dimages->exists($dimage)) {
      $path = $this->dimages->file($dimage);
      $image = IImage::make($path);
      return $image->response($dimage->ext);
    } else {
      $dsource = $dimage->source();
      if ($this->dimages->exists($dsource)) {
        $spath = $this->dimages->file($dsource);
        $dpath = $this->dimages->file($dimage);
        $image = IImage::make($spath);
        $p = config("dimages.profiles.$profile");
        $d = config("dimages.densities.$density");
        if (!$p) throw new DimagesException("Profile $profile invalid", 0xd9745b9921);
        if (!$d) throw new DimagesException("Density $density invalid", 0xd9745b9922);
        $w = $p[0]*$d;
        $h = $p[1]*$d;
        $image->fit($w, $h)->save($dpath);
        return $image->response($dimage->ext);
      }
    }
    throw new DimagesException("Image not found:$entity/$identity/$profile/$density/$index", 0xd9745b9920);
  }

  public function status() {
    return ['success' => true];
  }

  public function store(UploadDimageRequest $request, $entity, $identity) {
    $dimage = $this->dimages->store($entity, $identity, $request->image);
    return [
      'index' => $dimage->index
    ];
  }

  public function destroy($entity, $identity) {
    $this->dimages->destroy($entity, $identity);
  }
}