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
        $r = preg_match("/.+\/(?<domain>[^.]+)\.(?<slug>[^.]+)\.(?<index>[^.]+)\.(?<profile>[^.]+)\.(?<density>[^.]+)\.(?<id>[^.]+)\.(?<ext>[^.]+)$/", $file, $m);
        if ($r) {
            $record = new \stdClass;
            $record->id = 0 + $m['id'];
            $record->domain = $m['domain'];
            $record->slug   = $m['slug'];
            $record->index  = 0 + $m['index'];
            $record->profile = $m['profile'];
            $record->density = $m['density'];
            $record->ext = $m['ext'];
            return $record;
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
            $fileData = $this->getFileData($file);
            $s = config("dimages.profiles.$profile");
            if (empty($s)) throw new NotFoundHttpException("profile '$profile' does not exists.");
            $f = config("dimages.densities.$density");
            if (empty($f)) throw new NotFoundHttpException("density '$density' does not exists.");
            $w = intval($s[0] * $f);
            $h = intval($s[1] * $f);
            $iimage = IImage::make($file)->fit($w,$h);
            $iimage->save("$appPath/$domain.$slug.$i.$profile.$density.0.{$fileData->ext}");
            return $iimage->response();
        }
        $file = $sourceFiles[0];
        return IImage::make($file)->response();
    }
}