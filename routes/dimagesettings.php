<?php

Route::group([
  'namespace' => 'Marcohern\Dimages\Http\Controllers',
  'prefix' => 'dimagesettings'
], function () {
  Route::get('/{tenant}','DimageSettingsController@get'                               )->middleware('auth:api')->name('dimset-get');
  Route::post('/{tenant}/reset','DimageSettingsController@reset'                      )->middleware('auth:api')->name('dimset-reset');
  Route::post('/{tenant}/density','DimageSettingsController@storeDensity'             )->middleware('auth:api')->name('dimset-store-density');
  Route::post('/{tenant}/profile','DimageSettingsController@storeProfile'             )->middleware('auth:api')->name('dimset-store-profile');
  Route::delete('/{tenant}/density/{density}','DimageSettingsController@deleteDensity')->middleware('auth:api')->name('dimset-delete-density');
  Route::delete('/{tenant}/profile/{profile}','DimageSettingsController@deleteProfile')->middleware('auth:api')->name('dimset-delete-profile');
});