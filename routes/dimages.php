<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimages'
], function () {
    Route::get   ('status'                                                     , 'DimagesController@status'    )->middleware('auth:api')->name('dims-status');
    Route::get   ('session'                                                    , 'DimagesController@session'   )->middleware('auth:api')->name('dims-session');
    Route::post  ('{tenant}/{entity}/{identity}/switch/{source}/with/{target}' , 'DimagesController@switch'    )->middleware('auth:api')->name('dims-switch');
    Route::post  ('{tenant}/{entity}/{identity}/normalize'                     , 'DimagesController@normalize' )->middleware('auth:api')->name('dims-normalize');
    Route::get   ('{tenant}/{entity}/{identity}/sources'                       , 'DimagesController@sources'   )->middleware('auth:api')->name('dims-sources');
    Route::get   ('{tenant}/{entity}'                                          , 'DimagesController@identities')->middleware('auth:api')->name('dims-identities');
    Route::get   ('{tenant}'                                                   , 'DimagesController@entities'  )->middleware('auth:api')->name('dims-entities');
    Route::get   ('/'                                                          , 'DimagesController@tenants'   )->middleware('auth:api')->name('dims-tenants');
});