<?php

Route::group(['prefix' => '/dashboard', 'as' => 'dashboard.'], function () {
    Route::post('reseller-stats', 'DashboardController@resellerStats')->name('reseller-stats')->middleware('role:'.ROLE_RESELLER);
    Route::post('admin-stats', 'DashboardController@adminStats')->name('admin-stats')->middleware('role:'.ROLE_ADMIN);
});