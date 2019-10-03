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

// Agenda
Route::get('/obtener_agendas', 'agendaController@obtenerAgendas');
Route::post('/registrar_agenda', 'agendaController@registrarAgenda');
Route::get('/detalle_agenda/{id}', 'agendaController@detalleAgenda');
Route::post('/editar_agenda', 'agendaController@editarAgenda');
Route::post('/eliminar_agenda', 'agendaController@eliminarAgenda');

// Actas
Route::get('/datos_modal_acta', 'actaController@datosModalCreacion');
Route::post('/registrar_acta', 'actaController@registrarActa');
Route::get('/obtener_actas', 'actaController@obtenerActas');
Route::get('/detalle_acta/{id}', 'actaController@detalleActa');
Route::post('/editar_acta', 'actaController@editarActa');
Route::get('/puntos_agenda_acta/{id}', 'actaController@puntosAgenda');
Route::post('/detalle_punto_acta_agenda', 'actaController@detallePuntoActa');

// Mail
Route::get('/pdf_acta/{id_acta}', 'mailController@pdfActa');
Route::get('/enviar_correos', 'mailController@enviarCorreo');
Route::post('/enviar_agenda_aprobacion', 'mailController@enviarAgendaAprobacion');

// Puntos de la agenda
Route::post('/editar_punto', 'puntoAgendaController@editar');
Route::post('/eliminar_punto', 'puntoAgendaController@destroy');
Route::post('/reordenar', 'puntoAgendaController@reordenar');
Route::get('/bitacora_punto/{id}', 'puntoAgendaController@bitacoraPunto');
Route::get('/pdf_agenda/{id}', 'agendaController@pdfAgenda');

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

// Toma de asistencia
Route::get('/lista_asistencia/{id}', 'asistenciaController@listaAsistencia');
Route::post('/registrar_asistencia', 'asistenciaController@registrarAsistencia');
Route::post('/eliminar_asistencia', 'asistenciaController@eliminarAsistencia');
Route::post('/registrar_asistencia_especial', 'asistenciaController@registrarAsistenciaEspecial');
Route::post('/congelar_asistencia', 'asistenciaController@congelarAsistencia');

// Puntos de Acta
Route::post('/registro_punto_acta', 'actaController@registroPuntoActa');
Route::post('/editar_punto_acta', 'actaController@editarPuntoActa');
Route::get('/bitacora_punto_acta/{id}', 'actaController@bitacoraPunto');
Route::post('/eliminar_punto_acta', 'actaController@eliminarPuntoActa');

// Documentos
Route::post('/registrar_documento', 'documentoController@registrarDocumento');
Route::get('/obtener_documentos/{id}', 'documentoController@obtenerDocumentos');
Route::get('/detalle_documento/{id}', 'documentoController@detalleDocumento');
Route::get('/datos_modal_documento', 'documentoController@datosModal');
Route::post('/editar_documento', 'documentoController@editarDocumento');
Route::get('/eliminar_documento/{id}', 'documentoController@eliminarDocumento');
Route::get('/descargar_archivo/{id}', 'documentoController@descargarArchivo');