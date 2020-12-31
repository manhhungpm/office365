<?php

Route::group(['prefix' => '/license-config', 'as' => 'license-config.'], function () {
    Route::post('listing', 'LicenseConfigController@listing')->name('listing');
    Route::post('add', 'LicenseConfigController@add')->name('add');
});
