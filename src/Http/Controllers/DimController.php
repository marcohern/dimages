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
    return $this->dimages->dimages($entity, $identity);
  }

  public function images(string $entity, string $identity) {
    $dimages = $this->dimages->sources($entity, $identity);
    $urls = [];
    foreach ($dimages as $dimage) {
      $urls[] = DimageConstants::DIMROUTE."/$entity/$identity/{$dimage->index}";
    }
    return $urls;
  }

  public function entities(Request $request) {
    $entities = $this->dimages->entities();
    return $entities;
  }

  public function identities(Request $request, string $entity) {
    $identities = $this->dimages->identities($entity);
    return $identities;
  }

  public function move(string $src_ent, string $src_idn, string $trg_ent, string $trg_idn) {
    $sources = $this->dimages($src_ent, $src_idn);
    foreach ($sources as $source) {
      $target = clone $source;
      $target->entity = $trg_ent;
      $target->identity = $trg_idn;
      $this->dimages->move($source, $target);
    }
    $this->dimages->deleteIdentity($src_ent, $src_idn);
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
}