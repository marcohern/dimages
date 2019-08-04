<?php 
use Marcohern\Dimages\Lib\DimageConstants;

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimages2'
], function () {
    Route::get   ('status'                                                     , 'DimageController@status'     )->middleware('api')->name('dim-status');
    Route::get   ('session'                                                    , 'DimageController@session'    )->middleware('api')->name('dim-session');
    Route::post  ('{tenant}/move/{src_ent}/{src_idn}/to/{trg_ent}/{trg_idn}'   , 'DimageController@move'       )->middleware('api')->name('dim-move');
    Route::post  ('{tenant}/{entity}/{identity}/switch/{source}/with/{target}' , 'DimageController@switch'     )->middleware('api')->name('dim-switch');
    Route::post  ('{tenant}/{entity}/{identity}/normalize'                     , 'DimageController@normalize'  )->middleware('api')->name('dim-normalize');
    Route::get   ('{tenant}/{entity}/{identity}/dimages'                       , 'DimageController@index'      )->middleware('api')->name('dim-index');
    Route::get   ('{tenant}/{entity}/{identity}/images'                        , 'DimageController@images'     )->middleware('api')->name('dim-images');
    Route::get   ('{tenant}/{entity}/{identity}/sources'                       , 'DimageController@sources'    )->middleware('api')->name('dim-sources');
    Route::get   ('{tenant}/{entity}/{identity}/derivatives'                   , 'DimageController@derivatives')->middleware('api')->name('dim-derivatives');
    Route::get   ('{tenant}/{entity}/{identity}/{profile}/{density}/{index?}'  , 'DimageController@derive'     )->middleware('api')->name('dim-derive');
    Route::post  ('{tenant}/{entity}/{identity}/{index}'                       , 'DimageController@update'     )->middleware('api')->name('dim-update');
    Route::delete('{tenant}/{entity}/{identity}/{index?}'                      , 'DimageController@destroy'    )->middleware('api')->name('dim-destroy');
    Route::get   ('{tenant}/{entity}/{identity}/{index?}'                      , 'DimageController@source'     )->middleware('api')->name('dim-source');
    Route::post  ('{tenant}/{entity}/{identity}'                               , 'DimageController@store'      )->middleware('api')->name('dim-store');
    Route::get   ('{tenant}/{entity}'                                          , 'DimageController@identities' )->middleware('api')->name('dim-identities');
    Route::get   ('{tenant}'                                                   , 'DimageController@entities'   )->middleware('api')->name('dim-entities');
    Route::get   ('/'                                                          , 'DimageController@tenants'    )->middleware('api')->name('dim-tenants');
});