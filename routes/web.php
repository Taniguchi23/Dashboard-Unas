<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ValidacionController;

Route::get('/',function (){
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => ['auth', 'user.status']],function (){

    Route::group(['prefix'=>'admin','middleware'=>'isAdmin'], function (){
        Route::controller(AdminController::class)->group(function (){
            Route::get('/lista/{tipo}','lista')->name('admin.lista');
            Route::get('/filtros','filtros')->name('admin.filtro');
            Route::post('/usuario/store','usuarioStore')->name('admin.usuario.store');
            Route::get('/usuario/edit/{id}','usuarioEdit')->name('admin.usuario.edit');
            Route::post('/usuario/update/{id}','usuarioUpdate')->name('admin.usuario.update');
            Route::post('/filtro/store','filtroStore')->name('admin.filtro.store');
            Route::get('/filtro/edit/{id}','filtroEdit')->name('admin.filtro.edit');
            Route::post('/filtro/update/{id}','filtroUpdate')->name('admin.filtro.update');
        });
    });


    Route::controller(WebController::class)->group(function (){
        Route::get('/home','index')->name('home');
        Route::get('/vulnerabilidades','vulnerabilidades')->name('web.vulnerabilidades');
        Route::post('/vulnerabilidades','vulnerabilidades')->name('web.vulnerabilidadesPost');
        Route::get('/notificacion','notificacion')->name('web.notificacion');
        Route::get('/personalizacion','personalizacion')->name('web.personalizacion');
        Route::post('/personalizacion','personalizacionPost')->name('web.personalizacion.post');
    });

    Route::controller(DashController::class)->group(function (){
        Route::post('/service/suscribirse','suscribirse');
        Route::get('/service/vulnerabilidad/{id}','vulnerabilidad');
        Route::get('/service/verificarEmail/{email}','verificarEmail');
        Route::get('/service/graficos','graficos');
    });
});

Route::controller(ValidacionController::class)->group(function (){
    Route::get('recuperar-password','vista')->name('validacion.vista');
    Route::post('recuperar-password','recuperar')->name('validacion.recuperar');
    Route::get('recuperar-password/{token}','vistaRecuperar')->name('validacion.vistaRecuperar');
    Route::post('cambiar-password/{id}','cambiar')->name('validacion.cambiar');
});


Route::controller(DashController::class)->group(function (){
    Route::get('/service/consulta','consulta');
    Route::get('/service/consultaDominical','domingo');
});



