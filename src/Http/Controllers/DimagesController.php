<?php

namespace Marcohern\Dimages\Http\Controllers;

use Marcohern\Dimages\Lib\DimageId;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;

class DimagesController extends Controller {
    
    public function index() {
        return view('dimages::index');
    }

    private function getFileData($file) {
        $m = null;
        $r = preg_match("/(.+\/)?(?<domain>[^.]+)\.(?<slug>[^.]+)\.(?<index>[^.]+)\.(?<profile>[^.]+)\.(?<density>[^.]+)\.(?<id>[^.]+)\.(?<ext>[^.]+)$/", $file, $m);
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

    private function getTempFiles(Request $r) {
        $domain = $r->session()->get('dimages');
        $appPath = storage_path('app/mhn/dimages');
        $query = "$domain.*.000.*.*.*.*";
        $files = glob("$appPath/$query");
        $res = [];
        foreach ($files as $file) {
            $res[] = basename($file);
        }
        return $res;
        //domain.slug.index.profile.density.id.ext
        //barimages.tujaus.000.org.org.12345.jpg
    }

    private function setTempFileName(Request $r, string $slug, $id, string $ext) {
        
        $domain = $r->session()->get('dimages');
        return "$domain.$slug.000.org.org.$id.$ext";
    }

    public function upload(Request $r) {
        $domain = $r->session()->get('dimages',function() {
            return md5(uniqid('sess',true));
        });
        $r->session()->put('dimages', $domain);
        $files = $this->getTempFiles($r);

        return view('dimages::upload',['domain' => $domain, 'dimages' => $files]);
    }

    public function store(Request $r) {
        $filename = $r->dimage->getClientOriginalName();
        $ext = $r->dimage->getClientOriginalExtension();
        $id = DimageId::get();
        
        $domain = $r->session()->get('dimages');

        $slug = md5($filename.uniqid('mhn',true));

        $iimage = IImage::make($r->dimage);

        $r->dimage->storeAs('mhn/dimages',$this->setTempFileName($r,$slug,$id,$ext));
        return redirect()->route('dimages-upload');
    }

    public function attach(Request $r) {

        $appPath = storage_path('app/mhn/dimages');

        $domain = $r->session()->get('dimages');
        
        $i=0;
        $newDomain = $r->input('domain');
        $newSlug = $r->input('slug');
        $files = $this->getTempFiles($r);
        foreach ($files as $file) {
            
            $pi = str_pad($i, 3, "0", STR_PAD_LEFT);
            $fdata = $this->getFileData($file);
            //dd($file,$fdata);
            $id = $fdata->id;
            $ext = $fdata->ext;
            $newFilename = "$newDomain.$newSlug.$pi.org.org.$id.$ext";
            rename("$appPath/$file","$appPath/$newFilename");
            $i++;
        }

        return redirect()->route('dimages-index');
    }

    public function about() {
        return view('dimages::about');
    }

    public function api() {
        return response()->json([
            'success' => true,
            'api' => true
        ]);
    }
}