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
Route::get('/pdf_acta/{id_acta}', 'mailController@pdfActa');
Route::get('/enviar_correos', 'mailController@enviarCorreo');
Route::post('/enviar_agenda_aprobacion', 'mailController@enviarAgendaAprobacion');

// Puntos del acta
Route::post('/editar_punto', 'puntoAgendaController@editar');
Route::delete('/eliminar_punto/{id}', 'puntoAgendaController@destroy');
Route::post('/reordenar', 'puntoAgendaController@reordenar');

// Roles
Route::get('/obtener_roles', 'rolController@obtenerRoles');

// Personas
Route::get('/obtener_personas', 'personaController@obtenerPersonas');
Route::get('/detalle_persona/{id}', 'personaController@detallePersona');
Route::post('/editar_persona', 'personaController@editarPersona');
Route::get('/permisos_usuario/{id}', 'personaController@permisosUsuario');
Route::post('/registrar_persona', 'personaController@registrarPersona');
Route::get('/personas_correo', 'personaController@personasCorreo');

// Bitacora
Route::get('/bitacora_correo', 'bitacoraController@bitacoraCorreo');