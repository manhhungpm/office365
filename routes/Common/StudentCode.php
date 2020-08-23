<?php

Route::group(['prefix' => '/student-code', 'as' => 'studentCode.'], function () {
    Route::post('listing', 'StudentCodeController@listing')->name('listing');
    Route::post('store', 'StudentCodeController@store')->name('store');
    Route::post('edit', 'StudentCodeController@edit')->name('edit');
    Route::post('delete', 'StudentCodeController@delete')->name('delete');
    Route::post('list-all', 'StudentCodeController@listAll')->name('list-all');
});