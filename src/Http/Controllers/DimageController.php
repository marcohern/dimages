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
}