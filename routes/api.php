<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimapi',
    'middleware' => ['api']
], function () {
    Route::get('/', "DimagesController@api");
});