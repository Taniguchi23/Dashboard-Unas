<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\DashController;

Route::controller(WebController::class)->group(function (){
    Route::get('/','index')->name('index');
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
