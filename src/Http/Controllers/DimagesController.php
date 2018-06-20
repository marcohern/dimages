<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as IImage;

class DimagesController extends Controller {
    
    public function index() {
        return view('dimages::index');
    }

    public function upload(Request $r) {
        $domain = $r->session()->get('dimages',function() {
            return md5(uniqid('sess',true));
        });
        $r->session()->put('dimages', $domain);


        return view('dimages::upload',['domain' => $domain, 'dimages' => []]);
    }

    public function store(Request $r) {
        $filename = $r->dimage->getClientOriginalName();
        $ext = $r->dimage->getClientOriginalExtension();
        
        $domain = $r->session()->get('dimages');

        $slug = md5($filename.uniqid('mhn',true));

        $iimage = IImage::make($r->dimage);

        

        $r->dimage->storeAs('mhn/dimages',$dimage->filename);
        return redirect()->route('dimages-upload');
    }

    public function attach(Request $r) {

        $appPath = storage_path('app/mhn/dimages');

        $domain = $r->session()->get('dimages');
        
        $i=0;
        $newDomain = $r->input('domain');
        $newSlug = $r->input('slug');
/*
        $dimages = Dimage::where('domain',$domain)->get();
        foreach ($dimages as $dim) {
            $pi = str_pad($i, 3, "0", STR_PAD_LEFT);
            $oldFilename = $dim->filename;
            $newFilename = "$newDomain.$newSlug.$pi.{$dim->ext}";

            $dim->domain = $newDomain;
            $dim->slug = $newSlug;
            $dim->attached = 'TRUE';
            $dim->index = $i;
            $dim->filename = $newFilename;

            $dim->save();
            rename("$appPath/$oldFilename","$appPath/$newFilename");
            $i++;
        }*/

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