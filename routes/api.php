<?php 

$namespace = 'Marcohern\Dimages\Http\Controllers';

Route::group([
    'namespace' => $namespace,
    'prefix' => 'dimages/api',
    'middleware' => ['api']
], function () {
    Route::get('/', "DimagesController@api");
});