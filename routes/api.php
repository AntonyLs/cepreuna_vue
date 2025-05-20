<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


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

// Route::group(['prefix' => 'estudiante'], function () {
//     Route::get('/get-estudiante', 'Api\Estudiante\PerfilController@getEstudiante');
//     Route::get('/get-carga', 'Api\Estudiante\CursosController@getCarga');
// });
// Route::group(['prefix' => 'horario'], function () {
//     Route::get('/get-horario', 'Api\Estudiante\HorarioController@getHorario');
// });
// Route::group(['prefix' => 'social'], function () {
//     // Route::get('/token', 'Api\RedSocialController@token');
//     Route::get('/validate', 'Api\RedSocialController@validator');
// });
// Route::group(['prefix' => 'asistencia-estudiante'], function () {
//     Route::get('/get-asistencia', 'Api\Estudiante\AsistenciaController@getAsistencia');
// });
Route::group(['prefix' => 'pagos'], function () {
    // Route::get('/get-data', 'Api\Estudiante\PagoController@getDataPagos');
    Route::post('/validar-pago-cuota/{id}', 'Api\Estudiante\PagoController@validarPagoCuota');
    //curso
    Route::post('/validar-pago-curso', 'Api\Estudiante\PagoController@validarPagoCurso');
});


//CursoTaller
Route::post('v1/inscripcion/curso-taller', 'Api\InscripcionCursoTallerController@store');
Route::group(['prefix' => 'inscripciones'], function () {
    Route::resource('/curso', 'Api\InscripcionCursoTallerController');
    Route::get('/curso/pdf/{id}', 'Api\InscripcionCursoTallerController@pdf');
});


Route::group(['prefix' => 'perfil'], function () {
    // Route::get('/get-data', 'Api\Estudiante\PagoController@getDataPagos');
    Route::post('/guardar-foto/{id}', 'Api\Estudiante\PerfilController@guardarFoto');
    Route::get('/encrypt/{id}', 'Api\Estudiante\PerfilController@encrypt');
});
Route::group(['prefix' => 'v1/ficha'], function () {
    Route::post('/api-get-ficha', 'Api\Estudiante\FichaSimulacroController@getFicha');
});

Route::get('v1/{dni}', function (Request $request, $dni) {
    if ($request->header('Authorization') == "cepreuna_v1_api")
        return DB::select("SELECT concat(pe.inicio_ciclo,' - ',pe.fin_ciclo) AS periodo,pe.estado estado_periodo,
        es.nro_documento,es.nombres, es.paterno, es.materno,es.celular , sexo ,ub.departamento,
        ub.provincia,ub.distrito,ub.codigo_distrito ,anio_egreso,co.denominacion AS name_cole, co.direccion AS dir_cole,
        co.departamento AS dep_cole,co.provincia AS pro_cole,co.distrito AS dis_cole,tc.denominacion AS tipo_cole, co.tipo_colegios_id AS tipo,
        ap.nro_documento AS apo_documento,ap.nombres AS apo_nombres,ap.paterno AS apo_paterno,ap.materno AS apo_materno,ap.celular apo_celular,
        CASE WHEN ap.parentescos_id = 1 THEN 1 WHEN ap.parentescos_id = 2 THEN 2 ELSE 3 END AS apo_parentesco, ma.habilitado_estado AS habilitado FROM estudiantes es
        INNER JOIN inscripciones ins ON ins.estudiantes_id=es.id
        INNER JOIN periodos pe ON pe.id=ins.periodos_id
        LEFT  JOIN ubigeos ub ON es.ubigeos_id = ub.id
        INNER JOIN colegios co ON co.id = es.colegios_id
        INNER JOIN tipo_colegios tc ON tc.id = co.tipo_colegios_id
        LEFT JOIN estudiante_apoderados ea ON ea.estudiantes_id=es.id
        LEFT JOIN apoderados ap ON ap.id=ea.apoderados_id
        LEFT JOIN parentescos pa ON ap.parentescos_id=pa.id
        LEFT JOIN matriculas ma on  ma.estudiantes_id = es.id
        WHERE es.nro_documento=?", [$dni]);
});
// Route::get('v1/foto_estudiante/{dni}', function (Request $request, $dni) {
//     if ($request->header('Authorization') == "cepreuna_v1_api")
//         return DB::select("SELECT concat('https://sistemas.cepreuna.edu.pe/storage/fotos/',es.foto) foto_estudiante,
//         es.nro_documento,es.nombres, es.paterno,es.materno,es.nro_documento from estudiantes es
//         WHERE es.nro_documento=?", [$dni]);
// });

