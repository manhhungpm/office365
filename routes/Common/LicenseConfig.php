<?php

Route::group(['prefix' => '/license-config', 'as' => 'license-config.'], function () {
    Route::post('listing-license', 'LicenseConfigController@listing')->name('listing-license');
    Route::post('add-license', 'LicenseConfigController@add')->name('add-license');
});
