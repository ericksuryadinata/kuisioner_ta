<?php

/**
 * Routing untuk mengamankan dashboard admin dengan menggunakan middleware
 * Middleware bisa ditemukan di folder middleware
 * ------------------------------------------------------------------------
 * Sedikit penjelasan tentang penulisan routing
 * ------------------------------------------------------------------------
 * 
 * Contoh:
 * 
 * Route::group('super-admin', ['namespace' => 'Admin'], function(){
 * 
 * Penjelasan
 * 1. routing dengan menggunakan prefix super-admin, di url jadi localhost/super-admin
 * 2. namespace menunjukkan nama folder yang ada di controller
 * 
 * Karena di dalam group tidak bisa menggunakan namespace lagi, maka pada saat
 * 
 * Route::group('gate', function(){
 *      Route::get('in','Auth/AuthController@index')->name('admin.auth.index');
 * });
 * 
 * Tidak menggunakan namespace, melainkan langsung menuju folder yang dimaksud
 * 
 * Auth/AuthController@index -> ini menunjukkan folder Auth dengan file AuthController.php
 * dengan methode index, yang diberikan nama admin.auth.index, dimana ini untuk pemanggilan route()    
 * 
 */

Route::group('super-admin',['namespace' => 'Admin'],function(){
    Route::get('/','RedirectController@index')->name('admin.redirect.index');
    Route::group('gate',['namespace' => 'Auth'],function(){
        Route::get('in','AuthController@index')->name('admin.auth.index');
        Route::post('in/auth','AuthController@auth')->name('admin.auth.login');
        Route::get('out','AuthController@logout')->name('admin.auth.logout');    

    });

    Route::group('/', ['middleware' => ['AdminMiddleware']],function(){
        Route::get('dashboard','AdminController@index')->name('admin.dashboard');
        
        Route::group('/',['namespace' => 'Kuisioner'], function(){
            Route::get('/','AspekKuisionerController@index',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.index');
            Route::get('tambah','AspekKuisionerController@create',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.create');
            Route::get('datatable','AspekKuisionerController@datatable',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.datatable');
            Route::post('save','AspekKuisionerController@save',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.save');
            Route::get('edit/{id}','AspekKuisionerController@edit',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.edit');
            Route::post('update','AspekKuisionerController@update',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.update');
            Route::post('delete','AspekKuisionerController@delete',['prefix' => 'kuisioner/aspek'])->name('admin.kuisioner.aspek.delete');

            Route::get('/','KuisionerController@index',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.index');
            Route::get('tambah','KuisionerController@create',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.create');
            Route::get('upload','KuisionerController@upload',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.upload');
            Route::post('proses-upload','KuisionerController@uploadKuisioner',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.upload.proses');
            Route::get('datatable','KuisionerController@datatable',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.datatable');
            Route::post('save','KuisionerController@save',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.save');
            Route::get('edit/{id}','KuisionerController@edit',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.edit');
            Route::post('update','KuisionerController@update',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.update');
            Route::post('delete','KuisionerController@delete',['prefix' => 'kuisioner/pertanyaan'])->name('admin.kuisioner.pertanyaan.delete');

            Route::get('/','RekomendasiController@index',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.index');
            // Route::get('tambah','RekomendasiController@create',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.create');
            Route::get('upload','RekomendasiController@upload',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.upload');
            Route::post('proses-upload','RekomendasiController@uploadRekomendasi',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.upload.proses');
            Route::get('datatable','RekomendasiController@datatable',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.datatable');
            // Route::post('save','RekomendasiController@save',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.save');
            // Route::get('edit/{id}','RekomendasiController@edit',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.edit');
            // Route::post('update','RekomendasiController@update',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.update');
            // Route::post('delete','RekomendasiController@delete',['prefix' => 'kuisioner/rekomendasi'])->name('admin.kuisioner.rekomendasi.delete');
        });

        Route::group('/',['namespace' => 'Assessment'], function(){
            Route::get('/','AssessmentController@tabel',['prefix' => 'assessment/tabel'])->name('admin.assessment.tabel');
            Route::get('datatable','AssessmentController@datatable',['prefix' => 'assessment/tabel'])->name('admin.assessment.datatable');
            Route::get('upload','AssessmentController@upload',['prefix' => 'assessment/tabel'])->name('admin.assessment.tabel.upload');
            Route::post('proses-upload','AssessmentController@uploadAssessment',['prefix' => 'assessment/tabel'])->name('admin.assessment.tabel.upload.proses');
            Route::get('/','AssessmentController@grafik',['prefix' => 'assessment/grafik'])->name('admin.assessment.grafik');
            Route::get('grafik','AssessmentController@dataGrafik',['prefix' => 'assessment/grafik'])->name('admin.assessment.dataGrafik');
        });

        Route::group('/',['namespace' => 'Responden'], function(){
            Route::get('/','RespondenController@index',['prefix' => 'responden'])->name('admin.responden.index');
            Route::get('upload','RespondenController@upload',['prefix' => 'responden'])->name('admin.responden.upload');
            Route::post('proses-upload','RespondenController@uploadResponden',['prefix' => 'responden'])->name('admin.responden.upload.proses');
            Route::get('datatable','RespondenController@datatable',['prefix' => 'responden'])->name('admin.responden.datatable');
        });
    });
});
