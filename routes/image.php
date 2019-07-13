<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mh/dim'
], function () {
    Route::get('{entity}/{identity}/{profile}/{density}/{index?}', "DimController@full");
    Route::get('{entity}/{identity}/{index?}', "DimController@original");
});