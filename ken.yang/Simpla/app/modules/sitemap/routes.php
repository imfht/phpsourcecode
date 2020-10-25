<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  |
 */
Route::any('/sitemap', array('as' => 'sitemap', 'uses' => 'SitemapController@index'));