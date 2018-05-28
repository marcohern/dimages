<?php

namespace Marcohern\Dimages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marcohern\Dimages\Models\Dimage;
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

        $dimages = Dimage::all();

        return view('dimages::upload',['domain' => $domain, 'dimages' => $dimages]);
    }

    public function store(Request $r) {
        $filename = $r->dimage->getClientOriginalName();
        
        $domain = $r->session()->get('dimages');

        $slug = md5($filename.uniqid('mhn',true));

        $iimage = IImage::make($r->dimage);

        $dimage = new Dimage;

        $dimage->attached = 'FALSE';
        $dimage->domain = $domain;
        $dimage->slug = $slug;
        $dimage->profile = 'original';
        $dimage->density = 'original';
        $dimage->filename = '';
        $dimage->index = 0;
        $dimage->type = $iimage->mime();
        $dimage->width = $iimage->width();
        $dimage->height = $iimage->height();
        $dimage->parent_id = null;

        $dimage->save();

        $dimage->filename  = str_pad($dimage->id, 6, "0", STR_PAD_LEFT).'.'.$filename;

        $dimage->save();

        $r->dimage->storeAs('mhn/dimages',$dimage->filename);
        return redirect()->route('dimages-upload');
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