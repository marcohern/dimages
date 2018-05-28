<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mhn/dim'
], function () {
    Route::get('{profile}/{density}/{domain}/{slug}/{index?}', "DimController@full");
    Route::get('{domain}/{slug}/{index?}', "DimController@original");
});