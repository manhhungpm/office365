<?php

Route::group(['prefix' => '/user', 'as' => 'user.'], function () {
    Route::post('listing', 'UserController@listing')->name('listing');
    Route::post('store', 'UserController@store')->name('create');
    Route::post('edit', 'UserController@edit')->name('edit');
    Route::post('delete', 'UserController@delete')->name('store');
    Route::post('update-profile', 'UserController@updateProfile')->name('update-profile');
    Route::post('update-password', 'UserController@updatePassword')->name('update-password');
    Route::post('change-password', 'UserController@changePassword')->name('change-password');
    Route::get('get-role-list', 'RoleController@getRoleList')->name('get-role-list');
    Route::post('listing-all', 'UserController@listingAll')->name('listing-all');
    Route::post('listing-reseller', 'UserController@listingReseller')->name('listing-reseller');
    Route::post('listing-user-created', 'UserController@listingUserCreated')->name('listing-user-created');
});
