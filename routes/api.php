<?php 
use Marcohern\Dimages\Lib\DimageConstants;

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => DimageConstants::DIMROUTE
], function () {
    Route::get   ('status'                                            , 'DimController@status'     )->middleware('api'     )->name('dim-status');
    Route::get   ('session'                                           , 'DimController@session'    )->middleware('auth:api')->name('dim-session');
    Route::post  ('move/{src_ent}/{src_idn}/to/{trg_ent}/{trg_idn}'   , 'DimController@move'       )->middleware('auth:api')->name('dim-move');
    Route::post  ('{entity}/{identity}/switch/{source}/with/{target}' , 'DimController@switch'     )->middleware('auth:api')->name('dim-switch');
    Route::post  ('{entity}/{identity}/normalize'                     , 'DimController@normalize'  )->middleware('auth:api')->name('dim-normalize');
    Route::get   ('{entity}/{identity}/dimages'                       , 'DimController@index'      )->middleware('auth:api')->name('dim-index');
    Route::get   ('{entity}/{identity}/images'                        , 'DimController@images'     )->middleware('auth:api')->name('dim-images');
    Route::get   ('{entity}/{identity}/sources'                       , 'DimController@sources'    )->middleware('auth:api')->name('dim-sources');
    Route::get   ('{entity}/{identity}/derivatives'                   , 'DimController@derivatives')->middleware('auth:api')->name('dim-derivatives');
    Route::get   ('{entity}/{identity}/{profile}/{density}/{index?}'  , 'DimController@derive'     )->middleware('api'     )->name('dim-derive');
    Route::post  ('{entity}/{identity}/{index}'                       , 'DimController@update'     )->middleware('auth:api')->name('dim-update');
    Route::delete('{entity}/{identity}/{index?}'                      , 'DimController@destroy'    )->middleware('auth:api')->name('dim-destroy');
    Route::get   ('{entity}/{identity}/{index?}'                      , 'DimController@source'     )->middleware('api'     )->name('dim-source');
    Route::post  ('{entity}/{identity}'                               , 'DimController@store'      )->middleware('auth:api')->name('dim-store');
    Route::get   ('{entity}'                                          , 'DimController@identities' )->middleware('auth:api')->name('dim-identities');
    Route::get   ('/'                                                 , 'DimController@entities'   )->middleware('auth:api')->name('dim-entities');
});