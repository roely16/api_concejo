<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
    
});

Route::resource('agenda', 'agendaController');

Route::get('/puntos_agenda/{id}', 'puntoAgendaController@index');
Route::post('/registrar_punto_agenda', 'puntoAgendaController@store');

// Menu
Route::get('/menu_principal/{id_persona}', 'menuController@menuPrincipal');

// Login
Route::post('/login', 'usuarioController@login');

// Acta
Route::get('/datos_modal_acta', 'actaController@datosModalCreacion');
Route::post('/registrar_acta', 'actaController@registrarActa');
Route::get('/obtener_actas', 'actaController@obtenerActas');
Route::get('/detalle_acta/{id}', 'actaController@detalleActa');
Route::post('/editar_acta', 'actaController@editarActa');

// Mail
Route::get('/enviar_correos', 'mailController@sendMail');