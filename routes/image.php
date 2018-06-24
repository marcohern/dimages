<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mhn/dim'
], function () {
    Route::get('{domain}/{slug}/{profile}/{density}/{index?}', "DimController@full");
    Route::get('{domain}/{slug}/{index?}', "DimController@original");
});