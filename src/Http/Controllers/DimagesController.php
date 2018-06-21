<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;

class DimagesController extends Controller {
    
    public function index() {
        return view('dimages::index');
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

    private function setTempFileName(Request $r, string $slug, string $ext) {
        
        $domain = $r->session()->get('dimages');
        return "$domain.$slug.000.org.org.0.$ext";
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
        
        $domain = $r->session()->get('dimages');

        $slug = md5($filename.uniqid('mhn',true));

        $iimage = IImage::make($r->dimage);

        $r->dimage->storeAs('mhn/dimages',$this->setTempFileName($r,$slug,$ext));
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
            $newFilename = "$newDomain.$newSlug.$pi.org.org.0.jpg";
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