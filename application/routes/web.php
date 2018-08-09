<?php

/**
 * Welcome to Luthier-CI!
 *
 * This is your main route file. Put all your HTTP-Based routes here using the static
 * Route class methods
 *
 * Examples:
 *
 *    Route::get('foo', 'bar@baz');
 *      -> $route['foo']['GET'] = 'bar/baz';
 *
 *    Route::post('bar', 'baz@fobie', [ 'namespace' => 'cats' ]);
 *      -> $route['bar']['POST'] = 'cats/baz/foobie';
 *
 *    Route::get('blog/{slug}', 'blog@post');
 *      -> $route['blog/(:any)'] = 'blog/post/$1'
 */

/**
 * Routing untuk website
 * ------------------------------------------------------------------------
 * Sedikit penjelasan tentang penulisan routing
 * ------------------------------------------------------------------------
 * 
 * Contoh:
 * 
 * Route::group('/', ['namespace' => 'Website'], function(){
 * 
 * Penjelasan
 * 1. routing dengan menggunakan prefix /, di url jadi localhost/
 * 2. namespace menunjukkan nama folder yang ada di controller
 * 
 * Route::get('/', 'HomeController@index')->name('home.index');
 * 
 * prefix / menuju ke HomeController.php dengan penamaan home.index
 * home.index ini digunakan untuk memanggil via route()
 * jadi nanti pemanggilan dengan route('home.index')
 * ini menunjukkan HomeController bagian index
 * 
 */
Route::group('/',['namespace' => 'Website'],function(){
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::post('sign/up','HomeController@signUp')->name('home.signUp');
    Route::get('kuisioner','HomeController@kuisioner')->name('home.kuisioner');
    Route::get('kuisioner/selesai','HomeController@kuisionerSelesai')->name('home.kuisioner.selesai');
    Route::post('save','HomeController@saveStep')->name('home.kuisioner.save');
    Route::get('sign/out','HomeController@signOut')->name('home.signOut');
});

/**
 * Kita masukkan juga admin.php, tapi filenya dibedakan biar enak pengaturannya
 */
require __DIR__ . '/admin.php';

Route::set('404_override', function(){
    show_404();
});

Route::set('translate_uri_dashes',FALSE);