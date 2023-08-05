<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\AdminController;

Route::get('/',function (){
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware'=> 'auth'],function (){

    Route::group(['prefix'=>'admin','middleware'=>'auth'], function (){
        Route::controller(AdminController::class)->group(function (){
            Route::get('/lista/{tipo}','lista')->name('admin.lista');
            Route::get('/filtros}','filtros')->name('admin.filtro');
            Route::post('/usuario/store','usuarioStore')->name('admin.usuario.store');
            Route::get('/usuario/edit/{id}','usuarioEdit')->name('admin.usuario.edit');
            Route::post('/usuario/update/{id}','usuarioUpdate')->name('admin.usuario.update');
        });
    });


    Route::controller(WebController::class)->group(function (){
        Route::get('/home','index')->name('home');
        Route::get('/vulnerabilidades','vulnerabilidades')->name('web.vulnerabilidades');
        Route::get('/notificacion','notificacion')->name('web.notificacion');
        Route::get('/personalizacion','personalizacion')->name('web.personalizacion');
        Route::post('/personalizacion','personalizacionPost')->name('web.personalizacion.post');
    });

    Route::controller(DashController::class)->group(function (){
        Route::get('/service/consulta','consulta');
        Route::post('/service/suscribirse','suscribirse');
        Route::get('/service/vulnerabilidad/{id}','vulnerabilidad');
    });
});






