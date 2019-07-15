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

  public function index(Request $request, $entity, $identity) {
    $dimages = $this->dimages->list($entity, $identity);
    return $dimages;
  }

  public function entities(Request $request) {
    $entities = $this->dimages->entities();
    return $entities;
  }

  public function identities(Request $request, $entity) {
    $identities = $this->dimages->identities($entity);
    return $identities;
  }

  public function store(UploadDimageRequest $request, $entity, $identity) {
    $dimage = $this->dimages->store($entity, $identity, $request->image);
    return [
      'dimage' => $dimage,
      'url' => url($this->dimages->url($dimage))
    ];
  }

  public function view(Request $request, $entity, $identity, $index=0) {
    $dimage = $this->dimages->getSourceName($entity, $identity, $index);
    return [
      'dimage' => $dimage,
      'url' => url($this->dimages->url($dimage))
    ];
  }

  public function view_exact(Request $request, $entity, $identity, $profile, $density, $index=0) {
    $dimage = $this->dimages->viewExact($entity, $identity, $profile, $density, $index);
    return [
      'dimage' => $dimage,
      'url' => url($this->dimages->url($dimage))
    ];
  }

  public function destroy($entity, $identity) {
    return $this->dimages->destroy($entity, $identity);
  }
}
