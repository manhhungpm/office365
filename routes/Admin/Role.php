<?php

Route::group(['prefix' => '/role', 'as' => 'role.'], function () {
    Route::post('listing', 'RoleController@listing')->name('listing');
    Route::post('store', 'RoleController@store')->name('create');
    Route::post('edit', 'RoleController@edit')->name('edit');
    Route::post('delete', 'RoleController@delete')->name('store');
    Route::get('getpermissionlist','PermissionController@getPermissionList')->name('getpermissionlist');
});
