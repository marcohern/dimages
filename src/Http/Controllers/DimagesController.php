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

    public function upload() {
        return view('dimages::upload');
    }

    public function store(Request $r) {
        $filename = $r->dimage->getClientOriginalName();
        $ext = $r->dimage->getClientOriginalExtension();
        $fname = basename($filename, ".$ext");

        $iimage = IImage::make($r->dimage);

        $dimage = new Dimage;

        $dimage->attached = 'FALSE';
        $dimage->domain = 'temp';
        $dimage->slug = md5(uniqid('', true));
        $dimage->profile = 'original';
        $dimage->density = 'original';
        $dimage->filename = $r->dimage->getClientOriginalName();
        $dimage->type = $iimage->mime();
        $dimage->width = $iimage->width();
        $dimage->height = $iimage->height();
        $dimage->parent_id = null;


        $dimage->save();

        $dimage->filename  = str_pad($dimage->id, 6, "0", STR_PAD_LEFT).'.'.$dimage->filename;

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