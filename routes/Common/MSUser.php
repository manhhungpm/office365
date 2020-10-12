<?php

Route::group(['prefix' => '/ms-user', 'as' => 'msUser.'], function () {
    Route::post('listing', 'MSUserController@listing')->name('listing');
    Route::post('store', 'MSUserController@store')->name('create');
    Route::post('edit', 'MSUserController@edit')->name('edit');
    Route::post('delete', 'MSUserController@delete')->name('delete');
    Route::post('list-all', 'MSUserController@listAll')->name('list-all');
    Route::post('update-password', 'MSUserController@updatePassword')->name('update-password');
    Route::post('change-status', 'MSUserController@changeStatus')->name('change-status');
});
