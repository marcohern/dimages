<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Dimages\Models\Dimage;
use Intervention\Image\ImageManagerStatic as IImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DimController extends Controller
{
    public function original($domain, $slug, $index=null) {
        
        $appPath = storage_path("app/mhn/dimages");

        if (empty($index)) $index = 0;
        $dimage = Dimage::where('profile','original')
            ->where('density','original')
            ->where('domain',$domain)
            ->where('slug',$slug)
            ->where('index',$index)
            ->firstOrFail();

        $requestedFile = "$appPath/$domain.$slug.$index.{$dimage->ext}";
        if (file_exists($requestedFile)) {
            return IImage::make($requestedFile)->response();
        }
        throw new NotFoundHttpException("'$requestedFile' not found.");
    }

    public function full($profile, $density, $domain, $slug, $index=null) {
        $appPath = storage_path("app/mhn/dimages");

        $profiles = config('dimages.profiles');
        $densities = config('dimages.densities');

        if (!array_key_exists($profile, $profiles)) throw new NotFoundHttpException("profile '$profile' not found.");
        if (!array_key_exists($density, $densities)) throw new NotFoundHttpException("density '$density' not found.");

        $dp     = $profiles[$profile];
        $dp[0] *= $densities[$density];
        $dp[1] *= $densities[$density];

        if (empty($index)) $index = 0;
        
        $dimage = Dimage::where('profile',$profile)
            ->where('density',$density)
            ->where('domain',$domain)
            ->where('slug',$slug)
            ->where('index',$index)
            ->first();

        if (empty($dimage)) {
            $dimage = Dimage::where('profile','original')
                ->where('density','original')
                ->where('domain',$domain)
                ->where('slug',$slug)
                ->where('index',$index)
                ->firstOrFail();

            $requestedFile = "$appPath/$profile.$density.$domain.$slug.$index.{$dimage->ext}";
            $sourceFile = "$appPath/{$dimage->filename}";
            if (file_exists($requestedFile)) {
                return IImage::make($requestedFile)->response();
            }
            if (!file_exists($sourceFile)) {
                throw new NotFoundHttpException("'$sourceFile' not found.");
            }
            $iimage = IImage::make($sourceFile)->fit($dp[0],$dp[1]);
            $iimage->save($requestedFile);
            return $iimage->response();
        }
        $requestedFile = "$appPath/$profile.$density.$domain.$slug.$index.{$dimage->ext}";
        if (!file_exists($requestedFile)) {
            throw new NotFoundHttpException("'$requestedFile' not found.");
        }
        return IImage::make($requestedFile)->response();
    }
}