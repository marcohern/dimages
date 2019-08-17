<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimagesException;

use Marcohern\Dimages\Lib\Settings;
use Marcohern\Dimages\Lib\Factory;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\DimageManager;
use Marcohern\Dimages\Lib\Managers\ImageManager;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Http\Requests\UploadDimageRequest;

class DimageSettingsController extends Controller {
  
  protected $factory;

  public function __construct(Factory $factory) {
    $this->factory = $factory;
  }

  public function get(string $tenant) {
    return [
      'settings'=>$this->factory->loadSettings($tenant)
    ];
  }

  public function storeDensity(Request $request, string $tenant) {
    $settings = $this->factory->loadSettings($tenant);
    $settings->setDensity($request->name, $request->value);
    $settings->save();
  }

  public function storeProfile(Request $request, string $tenant) {
    $settings = $this->factory->loadSettings($tenant);
    $settings->setProfile($request->name, $request->width, $request->height);
    $settings->save();
  }

  public function deleteDensity(Request $request, string $tenant, string $density) {
    $settings = $this->factory->loadSettings($tenant);
    $settings->deleteDensity($density);
    $settings->save();
  }

  public function deleteProfile(Request $request, string $tenant, string $profile) {
    $settings = $this->factory->loadSettings($tenant);
    $settings->deleteProfile($profile);
    $settings->save();
  }

  public function reset(string $tenant) {
    $this->factory->settings($tenant)->save();
  }
}