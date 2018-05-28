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

        $iimage = IImage::make($r->dimage);

        $dimage = new Dimage;

        $dimage->attached = 'FALSE';
        $dimage->domain = 'global';
        $dimage->slug = 'test';
        $dimage->profile = 'original';
        $dimage->density = 'original';
        $dimage->filename = 'foo.jpg';
        $dimage->type = $iimage->mime();
        $dimage->width = $iimage->width();
        $dimage->height = $iimage->height();
        $dimage->parent_id = null;

        $dimage->save();

        $r->dimage->storeAs('mhn/dimages',$dimage->filename);
        return redirect()->route('dimages-upload');
    }

    public function api() {
        return response()->json([
            'success' => true,
            'api' => true
        ]);
    }
}