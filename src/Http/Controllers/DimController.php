<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DimController extends Controller
{

    private function getFileData($file) {
        $m = null;
        $r = preg_match("/(?<domain>[^.]+)\.(?<slug>[^.]+)\.(?<index>[^.]+)\.(?<profile>[^.]+)\.(?<density>[^.]+)\.(?<id>[^.]+)\.(?<ext>[^.]+)/", $file, $m);
        if ($r) {
            return $m;
        }
        return false;
    }

    public function original($domain, $slug, $index=null) {
        
        $appPath = storage_path("app/mhn/dimages");
        
        if (empty($index)) $index = 0;
        $i = str_pad($index, 3, "0", STR_PAD_LEFT);
        $query = "$appPath/$domain.$slug.$i.org.org.*.*";
        $files = glob($query);
        //dd($query);
        if (array_key_exists(0,$files)) {
            $file = $files[0];
            if (file_exists($file)) {
                return IImage::make($file)->response();
            }
            throw new NotFoundHttpException("'$file' does not exists.");
        }
        throw new NotFoundHttpException("Searching for '$query' yielded no results.");
    }

    public function full($domain, $slug, $profile, $density, $index=null) {
        $appPath = storage_path("app/mhn/dimages");

        if (empty($index)) $index = 0;
        $i = str_pad($index, 3, "0", STR_PAD_LEFT);

        $sourceQuery = "$appPath/$domain.$slug.$i.$profile.$density.*.*";
        $file = null;
        $sourceFiles = glob($sourceQuery);
        if (!array_key_exists(0, $sourceFiles)) {
            $originalQuery = "$appPath/$domain.$slug.$i.org.org.*.*";
            $originalFiles = glob($originalQuery);
            if (!array_key_exists(0, $originalFiles)) {
                throw new NotFoundHttpException("Searching for '$originalQuery' and '$sourceQuery' yielded no results.");
            }
            $file = $originalFiles[0];
            $iimage = IImage::make($file)->fit(64,64);
            $iimage->save("$appPath/$domain.$slug.$i.$profile.$density.0.jpg");
            return $iimage->response();
        }
        $file = $files[0];
        return IImage::make($file)->response();
    }
}