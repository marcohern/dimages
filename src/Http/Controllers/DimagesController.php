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
use Marcohern\Dimages\Lib\Managers\ImageManager;
use Marcohern\Dimages\Lib\Managers\StorageManager;
use Marcohern\Dimages\Http\Requests\UploadDimageRequest;

class DimagesController extends Controller {
  
}