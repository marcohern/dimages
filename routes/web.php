<?php 

$namespace = 'Marcohern\Dimages\Http\Controllers';

Route::group([
    'namespace' => $namespace,
    'prefix' => 'dimages',
    'middleware' => ['web']
], function () {
    Route::get('/', "DimagesController@index");
});