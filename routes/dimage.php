<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimage'
], function () {
    Route::post  ('stage/{tenant}/{session}'                                   , 'DimageController@stage'       )->middleware('auth:api')->name('dim-stage');
    Route::post  ('attach/{tenant}/{session}/{entity}/{identity}'              , 'DimageController@attach'      )->middleware('auth:api')->name('dim-attach');
    Route::get   ('{tenant}/{entity}/{identity}/{profile}/{density}/{index?}'  , 'DimageController@derive'      )->middleware('api'     )->name('dim-derive');
    Route::post  ('{tenant}/{entity}/{identity}/{index}'                       , 'DimageController@update'      )->middleware('auth:api')->name('dim-update');
    Route::delete('{tenant}/{entity}/{identity}/{index}'                       , 'DimageController@destroyIndex')->middleware('auth:api')->name('dim-destroyIndex');
    Route::get   ('{tenant}/{entity}/{identity}/{index?}'                      , 'DimageController@source'      )->middleware('api'     )->name('dim-source');
    Route::post  ('{tenant}/{entity}/{identity}'                               , 'DimageController@store'       )->middleware('auth:api')->name('dim-store');
    Route::delete('{tenant}/{entity}/{identity}'                               , 'DimageController@destroy'     )->middleware('auth:api')->name('dim-destroy');
});