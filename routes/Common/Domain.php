<?php

Route::group(['prefix' => '/domain', 'as' => 'domain.'], function () {
    Route::post('listing', 'DomainController@listing')->name('listing');
    Route::post('store', 'DomainController@store')->name('store');
    Route::post('edit', 'DomainController@edit')->name('edit');
    Route::post('delete', 'DomainController@delete')->name('delete');
    Route::post('list-all', 'DomainController@listAll')->name('list-all');
});