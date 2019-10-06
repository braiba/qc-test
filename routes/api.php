<?php

Route::group(['namespace' => 'Api'], function () {
    Route::get('gif/search', ['uses' => 'GifController@search']);
    Route::get('gif/random', ['uses' => 'GifController@random']);
});
