<?php

namespace Marcohern\Dimages\Http\Controllers;

use App\Http\Controllers\Controller;

class DimagesController extends Controller {
    
    public function index() {
        return view('dimages::index');
    }

    public function api() {
        return response()->json([
            'success' => true,
            'api' => true
        ]);
    }
}