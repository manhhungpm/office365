<?php

Route::group(['namespace' => 'Common','prefix' => '/logs', 'as' => 'logs.'], function () {
    Route::post('listing', 'LogsController@listing')->name('listing');
});
