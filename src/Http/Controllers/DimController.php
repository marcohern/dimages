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
    $dimage = $this->dimages->getSourceName($entity, $identity, $index);
    $content = $this->dimages->content($dimage);
    $image = IImage::make($content);
    return $image->response($dimage->ext);
  }

  public function full($entity, $identity, $profile, $density, $index=0) {
    $dimage = $this->dimages->getName($entity, $identity, $profile, $density, $index);
    if ($this->dimages->exists($dimage)) {
      $content = $this->dimages->content($dimage);
      $image = IImage::make($content);
      return $image->response($dimage->ext);
    } else {
      $dsource = $dimage->source();
      if ($this->dimages->exists($dsource)) {
        $source = $this->dimages->content($dsource);
        $image = IImage::make($source);
        $p = config("dimages.profiles.$profile");
        $d = config("dimages.densities.$density");

        if (!$p) throw new DimagesException("Profile $profile invalid", 0xd9745b9921);
        if (!$d) throw new DimagesException("Density $density invalid", 0xd9745b9922);
        $w = $p[0]*$d;
        $h = $p[1]*$d;
        $derived = (string) $image->fit($w, $h)->encode($dimage->ext);
        $this->dimages->put($dimage, $derived);
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