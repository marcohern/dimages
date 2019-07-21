<?php 
use Marcohern\Dimages\Lib\DimageConstants;

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => DimageConstants::DIMROUTE,
    'middleware' => ['api']
], function () {
    Route::get   ('status'                                            , 'DimController@status'     )->name('dim-status');
    Route::post  ('{entity}/{identity}/switch/{source}/with/{target}' , 'DimController@switch'     )->name('dim-switch');
    Route::post  ('{entity}/{identity}/normalize'                     , 'DimController@normalize'  )->name('dim-normalize');
    Route::get   ('{entity}/{identity}/dimages'                       , 'DimController@index'      )->name('dim-index');
    Route::get   ('{entity}/{identity}/sources'                       , 'DimController@sources'    )->name('dim-sources');
    Route::get   ('{entity}/{identity}/derivatives'                   , 'DimController@derivatives')->name('dim-derivatives');
    Route::get   ('{entity}/{identity}/{profile}/{density}/{index?}'  , 'DimController@derive'     )->name('dim-derive');
    Route::post  ('{entity}/{identity}/{index}'                       , 'DimController@update'     )->name('dim-update');
    Route::delete('{entity}/{identity}/{index?}'                      , 'DimController@destroy'    )->name('dim-destroy');
    Route::get   ('{entity}/{identity}/{index?}'                      , 'DimController@source'     )->name('dim-source');
    Route::post  ('{entity}/{identity}'                               , 'DimController@store'      )->name('dim-store');
    Route::get   ('{entity}'                                          , 'DimController@identities' )->name('dim-identities');
    Route::get   ('/'                                                 , 'DimController@entities'   )->name('dim-entities');
});