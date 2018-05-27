<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimages',
    'middleware' => ['web']
], function () {
    Route::get('/'      , "DimagesController@index")->name('dimages-index');
    Route::get('/upload', "DimagesController@upload")->name('dimages-upload');
});