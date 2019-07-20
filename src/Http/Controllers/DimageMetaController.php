<?php

namespace Marcohern\Dimages\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Marcohern\Dimages\Http\Requests\UploadDimageRequest;
use Marcohern\Dimages\Lib\DimageManager;

class DimageMetaController extends Controller
{
  protected $dimages;

  public function __construct(DimageManager $dimages) {
    $this->dimages = $dimages;
  }

  public function index(Request $request, string $entity, string $identity) {
    $dimages = $this->dimages->dimages($entity, $identity);
    return $dimages;
  }

  public function entities(Request $request) {
    $entities = $this->dimages->entities();
    return $entities;
  }

  public function identities(Request $request, string $entity) {
    $identities = $this->dimages->identities($entity);
    return $identities;
  }

  public function store(UploadDimageRequest $request, string $entity, string $identity) {
    $dimage = $this->dimages->storeIdentity($entity, $identity, $request->image);
    return [
      'dimage'  => $dimage,
      'url'     => url($this->dimages->url($dimage)),
    ];
  }

  public function view(Request $request, string $entity, string $identity, $index=0) {
    $dimage = $this->dimages->source($entity, $identity, $index);
    return [
      'dimage'  => $dimage,
      'url'     => url($this->dimages->url($dimage)),
    ];
  }

  public function view_exact(Request $request, string $entity, string $identity, string $profile, string $density, $index=0) {
    $dimage = $this->dimages->get($entity, $identity, $profile, $density, $index);
    return [
      'dimage' => $dimage,
      'url' => url($this->dimages->url($dimage)),
    ];
  }

  public function destroy($entity, $identity) {
    return $this->dimages->deleteIdentity($entity, $identity);
  }

  public function switch_index(string $entity, string $identity, Request $request) {
    $source = $request->source;
    $target = $request->target;
    $this->dimages->switchIndex($entity, $identity, $source, $target);
  }
}
