<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Lib\Dimager;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;

class DimController extends Controller
{
    public function original($domain, $slug, $index=null) {
        
        $appPath = storage_path("app/mhn/dimages");
        $dimager = new Dimager($appPath);
        $dimage = $dimager->getSource($domain, $slug, $index);

        $requestedFile = "$appPath/".$dimage->getFileName();
        if (file_exists($requestedFile)) {
            return IImage::make($requestedFile)->response();
        }
        throw new NotFoundHttpException("'$requestedFile' not found.");
    }

    public function full($domain, $slug, $profile, $density, $index=null) {
        $appPath = storage_path("app/mhn/dimages");
        $dimager = new Dimager($appPath);

        $profiles = config('dimages.profiles');
        $densities = config('dimages.densities');
        //dd($profiles, $profile,$densities, $density);

        if (!array_key_exists($profile, $profiles)) throw new NotFoundHttpException("profile '$profile' not found.");
        if (!array_key_exists($density, $densities)) throw new NotFoundHttpException("density '$density' not found.");

        //dd($domain, $slug, $index, $profile, $density);
        $size = $profiles[$profile];
        $factor = $densities[$density];
        $w = intval($size[0] * $factor);
        $h = intval($size[1] * $factor);
        $requestedFile = null;
        
        try {
            $dimage = $dimager->getSource($domain, $slug, $index, $profile, $density);
            $requestedFile = "$appPath/".$dimage->getFileName();
            return IImage::make($requestedFile)->response();
        } catch (DimageNotFoundException $ex1) {
            
            try {
                $dimage = $dimager->getSource($domain, $slug, $index);
                $requestedFile = "$appPath/".$dimage->getDerivedFileName($profile, $density);
                $sourceFile = "$appPath/".$dimage->getFileName();
                
                $iimage = IImage::make($sourceFile)->fit($w,$h);
                $iimage->save($requestedFile);
                return $iimage->response();
            } catch (DimageNotFoundException $ex2) {
                throw new NotFoundHttpException("'$requestedFile' not found.", $ex2);
            }
            throw new NotFoundHttpException("'$requestedFile' not found.", $ex1);
        }
    }
}