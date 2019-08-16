<?php

Route::group([
  'namespace' => 'Marcohern\Dimages\Http\Controllers',
  'prefix' => 'dimagesettings'
], function () {
  Route::get('/','DimageSettingsController@get')->middleware('api')->name('dimset-get');
  Route::put('/','DimageSettingsController@put')->middleware('api')->name('dimset-put');
});