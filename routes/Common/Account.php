<?php

Route::group(['prefix' => '/account', 'as' => 'account.'], function () {
    Route::post('listing', 'AccountController@listing')->name('listing');
    Route::post('store', 'AccountController@store')->name('create');
    Route::post('edit', 'AccountController@edit')->name('edit');
    Route::post('delete', 'AccountController@delete')->name('delete');
    Route::post('change-status', 'AccountController@changeStatus')->name('change-status');
    Route::post('list-all', 'AccountController@listAll')->name('list-all');
    Route::post('get-license', 'AccountController@getLicense')->name('get-license');
});
