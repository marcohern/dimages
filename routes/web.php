<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mhn/dimages',
    'middleware' => ['web']
], function () {
    Route::get ('/index' , "DimagesController@index" )->name('dimages-index' );
    Route::get ('/upload', "DimagesController@upload")->name('dimages-upload');
    Route::post('/store' , "DimagesController@store" )->name('dimages-store' );
    Route::get ('/about' , "DimagesController@about" )->name('dimages-about' );
});