<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimages'
], function () {
    Route::get   ('status'                                                     , 'DimagesController@status'    )->middleware('api')->name('dims-status');
    Route::get   ('session'                                                    , 'DimagesController@session'   )->middleware('api')->name('dims-session');
    Route::post  ('{tenant}/{entity}/{identity}/switch/{source}/with/{target}' , 'DimagesController@switch'    )->middleware('api')->name('dims-switch');
    Route::post  ('{tenant}/{entity}/{identity}/normalize'                     , 'DimagesController@normalize' )->middleware('api')->name('dims-normalize');
    Route::get   ('{tenant}/{entity}/{identity}/sources'                       , 'DimagesController@sources'   )->middleware('api')->name('dims-sources');
    Route::get   ('{tenant}/{entity}'                                          , 'DimagesController@identities')->middleware('api')->name('dims-identities');
    Route::get   ('{tenant}'                                                   , 'DimagesController@entities'  )->middleware('api')->name('dims-entities');
    Route::get   ('/'                                                          , 'DimagesController@tenants'   )->middleware('api')->name('dims-tenants');
});