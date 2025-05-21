<?php

use App\Models\Tarifa;
use App\Models\Estudiante;
use App\Models\DocenteApto;
use App\Models\ModelHasRole;
use App\Services\GWorkspace;
use App\Models\Inscripciones;
use App\Models\InscripcionPago;
use App\Models\TarifaEstudiante;
use App\Models\InscripcionDocente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('/urlget_d', function () {

    $docentes = DocenteApto::orderBy('id', "desc")->get();
    foreach ($docentes as $key => $value) {
        $query = ModelHasRole::where("role_id", 13)->where("model_id", $value->id)->first();
        if ($query) {
            echo "existe <br>";
        } else {
            echo "no existe <br>";
            DB::beginTransaction();
            try {
                //code..
                $data = new ModelHasRole;
                $data->role_id = 13;
                $data->model_type = 'App\Models\DocenteApto';
                $data->model_id = $value->id;
                $data->save();
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                echo $value->id . "<br>";
            }
        }
        // DB::beginTransaction();
        // try {
        //     //code..
        //     $data = new ModelHasRole;
        //     $data->role_id = 13;
        //     $data->model_type = 'App\Models\DocenteApto';
        //     $data->model_id = $value->id;
        //     $data->save();
        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollback();
        //     echo $value->id."<br>";
        // }

    }
});
Route::get('/urlget_e', function () {

    $estudiante = Estudiante::orderBy('id', "desc")->get();
    $i = 1;
    foreach ($estudiante as $key => $value) {
        $query = ModelHasRole::where("role_id", 12)->where("model_id", $value->id)->first();
        if ($query) {
            // echo "existe <br>";
        } else {
            // echo "no existe <br>";
            // echo $i++;
            // echo $value->nro_documento . ',';
            DB::beginTransaction();
            try {
                //code..
                $data = new ModelHasRole;
                $data->role_id = 12;
                $data->model_type = 'App\Models\Estudiante';
                $data->model_id = $value->id;
                $data->save();
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                echo $value->id . "<br>";
            }
        }
        // DB::beginTransaction();
        // try {
        //     //code..
        //     $data = new ModelHasRole;
        //     $data->role_id = 13;
        //     $data->model_type = 'App\Models\DocenteApto';
        //     $data->model_id = $value->id;
        //     $data->save();
        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollback();
        //     echo $value->id."<br>";
        // }

    }
});
// Route::get('/urlget_e', function () {

//     $docentes = Estudiante::orderBy('id',"desc")->get();
//     foreach ($docentes as $key => $value) {
//         DB::beginTransaction();
//         try {
//             //code..
//             $data = new ModelHasRole;
//             $data->role_id = 12;
//             $data->model_type = 'App\Models\Estudiante';
//             $data->model_id = $value->id;
//             $data->save();
//             DB::commit();
//         } catch (\Throwable $th) {
//             DB::rollback();
//             echo $value->id."<br>";
//         }

//     }
// });



Route::get('dga/estudiantes/pdf-constancia/{id}', 'Web\Inscripcion\EstudianteController@pdfConstancia');
Route::get('dga/estudiantes/foto/{id}', 'Web\Inscripcion\EstudianteController@getFotografia');

Auth::routes();

// login google
// Route::namespace('Auth\Login')->group(function() {
//     Route::get('/web/login', function () {
//         return view('auth/login/login-google');
//     });
//     Route::post('web/logout', 'Web/@logout')->name('web.logout');
// });
Route::get('web/login/google', 'Auth\Login\LoginGoogleController@redirectToProvider');
Route::get('web/login/google/callback', 'Auth\Login\LoginGoogleController@handleProviderCallback');
// fin

// Route::namespace('Auth\Login')->group(function() {
//     Route::get('/docente/login', function () {
//         return view('auth/login/login-docente');
//     });
//     Route::post('docente/logout', 'Web/@logout')->name('referencia.logout');
// });
// Route::get('docente/login/google', 'Auth\Login\LoginDocenteController@redirectToProvider');
// Route::get('docente/login/google/callback', 'Auth\Login\LoginDocenteController@handleProviderCallback');

// Route::namespace('Auth\Login')->group(function() {
//     Route::get('/estudiante/login', function () {
//         return view('auth/login/login-estudiante');
//     });
//     Route::post('estudiante/logout', 'Web/@logout')->name('referencia.logout');
// });
// Route::get('estudiante/login/google', 'Auth\Login\LoginEstudianteController@redirectToProvider');
// Route::get('estudiante/login/google/callback', 'Auth\Login\LoginEstudianteController@handleProviderCallback');
// rutas panel docente
// Route::get('comunicado/mostrar/{mostrar}', 'Intranet\ComunicadoController@mostrar');
Route::post('asistencia/docente/externo/image', 'Intranet\Auxiliar\AsistenciaDocenteController@saveImageExterno');

Route::group(['prefix' => 'estudiante', 'middleware' => 'redirect_app'], function () {
    Route::get('/dashboard', 'Web\Estudiante\DashboardController@index');
    Route::get('/get-local-estudiante', 'Web\Estudiante\DashboardController@getLocalEstudiante');
    Route::get('/perfil', 'Web\Estudiante\PerfilController@index');
    Route::post('/confirmar-datos', 'Web\Estudiante\PerfilController@confirmarDatos');
    Route::post('/actualizar-datos', 'Web\Estudiante\PerfilController@actualizarDatos');
    Route::get('/get-estudiante', 'Web\Estudiante\PerfilController@getEstudiante');
    Route::get('/pdf-declaracion-jurada/{id}', 'Web\Estudiante\PerfilController@pdfDeclaracionJurada');

    Route::group(['prefix' => 'cursos'], function () {
        Route::get('/', 'Web\Estudiante\CursosController@index');
        Route::get('get-carga', 'Web\Estudiante\CursosController@getCarga');
        Route::get('get-criterios-docente', 'Web\Estudiante\CursosController@getCriteriosDocente');
        Route::post('carga-update', 'Web\Estudiante\CursosController@storeLink');
        Route::get('cuadernillo', 'Web\Estudiante\CursosController@indexCuadernillo');
        Route::get('get-cursos-estudiante', 'Web\Estudiante\CursosController@getCursosEstudiante');
        Route::get('get-url-cuadernillo', 'Web\Estudiante\CursosController@getUrlCuadernillo');
        Route::post('calificar-docente/{id}', 'Web\Estudiante\CursosController@CalificarDocente');
    });
    Route::put('/matricular/{id}', 'Web\Estudiante\DashboardController@matricular');
    Route::get('/get-carga-academica', 'Web\Estudiante\DashboardController@getCargaAcademica');
    Route::get('/check-matricula/{id}', 'Web\Estudiante\DashboardController@checkMatricula');

    Route::group(['prefix' => 'horario'], function () {
        Route::get('/', 'Web\Estudiante\HorarioController@index');
        Route::get('/get-horario', 'Web\Estudiante\HorarioController@getHorario');
    });

    Route::group(['prefix' => 'asistencia'], function () {
        Route::get('/', 'Web\Estudiante\AsistenciaController@index');
        Route::get('/get-asistencias', 'Web\Estudiante\AsistenciaController@getAsistencia');
        Route::get('/get-rango-fechas', 'Web\Estudiante\AsistenciaController@rangoFechas');
    });
    Route::group(['prefix' => 'pago'], function () {
        Route::get('/', 'Web\Estudiante\PagoController@index');
        Route::get('/resumen-pago', 'Web\Estudiante\PagoController@getResumenPago');
        Route::get('/vouchers-pago', 'Web\Estudiante\PagoController@getVouchersPago');
        Route::post('validar-pago-cuota/', 'Web\Estudiante\PagoController@validarPagoCuota');
        Route::post('registrar-pago-cuota/', 'Web\Estudiante\PagoController@registrarPagoCuota');
        Route::post('registrar-pago-cuota-mora/', 'Web\Estudiante\PagoController@registrarPagoCuotaMora');
    });
});

Route::group(['prefix' => 'docente', 'middleware' => 'redirect_app'], function () {
    // return redirect('https://app.cepreuna.edu.pe');
    Route::get('/dashboard', 'Web\Docente\DashboardController@index');
    Route::get('/perfil', 'Web\Docente\PerfilController@index');
    Route::get('/get-docente', 'Web\Docente\PerfilController@getDocente');

    Route::group(['prefix' => 'cursos'], function () {
        Route::get('/', 'Web\Docente\CursosController@index');
        Route::get('get-carga', 'Web\Docente\CursosController@getCarga');
        Route::post('carga-update', 'Web\Docente\CursosController@storeLink');
        Route::get('cuadernillo', 'Web\Docente\CursosController@indexCuadernillo');
        Route::get('get-cursos-docente', 'Web\Docente\CursosController@getCursosDocente');
        Route::get('get-cursos-docente-temario', 'Web\Docente\CursosController@getCursosDocenteTemario');
        Route::get('get-url-cuadernillo', 'Web\Docente\CursosController@getUrlCuadernillo');
        Route::get('temario', 'Web\Docente\CursosController@indexTemario');
        Route::get('sesion', 'Web\Docente\CursosController@indexSesiones');
        Route::get('sesion/{id}/edit', 'Web\Docente\CursosController@editSesion');
        Route::post('store-sesion', 'Web\Docente\CursosController@storeSesion');
        Route::put('update-sesion/{id}', 'Web\Docente\CursosController@updateSesion');
        Route::get('sesion/lista/data', 'Web\Docente\CursosController@listaSesion');
        Route::get('get-cursos-carga', 'Web\Docente\CursosController@getCursosCarga');
        Route::get('get-estudiantes/{id}', 'Web\Docente\CursosController@getEstudiantes');
    });

    Route::group(['prefix' => 'horario'], function () {
        Route::get('/', 'Web\Docente\HorarioController@index');
        Route::get('/get-horario', 'Web\Docente\HorarioController@getHorario');
    });
    Route::group(['prefix' => 'asistencia'], function () {
        Route::get('/', 'Web\Docente\AsistenciaController@index');
        Route::get('/get-asistencias', 'Web\Docente\AsistenciaController@getAsistencia');
    });
});

Route::get('/web/login', ['middleware' => 'redirect_app', function () {

    if (Auth::guard('docente')->check()) {
        return redirect('/docente/dashboard');
    } elseif (Auth::guard('estudiante')->check()) {
        return redirect('/estudiante/dashboard');
    } else {
        return view('auth/login/login-google');
    }
}]);
Route::get('/docente/login', function () {
    return redirect('/');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/generate/token/{id}', function ($id) {

        $token = Crypt::encryptString($id);
        dd($token);
    });
    Route::get('/tarifa-estudiantes-save', function () {
        Artisan::call('tarifa:task');
    });
    Route::get('/tarifa-estudiantes', function () {

        $inscripciones  =  DB::table("inscripciones as i")
            ->select("*")
            ->where("i.estado", "1")
            ->get();
        // dd($inscripciones);
        // 1:normal  2:hijo de trabajador 3:descuento trabajdor UNA 4: hermanos 5: inscripcion por RR: 6 servicio militar

        // $tarifaMensual = Tarifa::where([
        //     ['modalidad', $inscripcion->modalidad],
        //     ['concepto_pagos_id', '2'],
        //     ['tipo_estudiante', $descuento]
        // ])->first();

        foreach ($inscripciones as $key => $inscripcion) {

            $descuento = '0';
            switch ($inscripcion->tipo_estudiante) {
                case '1':
                    $descuento = '1';
                    break;
                case '2':
                    $descuento = '2';
                    break;
                case '3':
                    $descuento = '2';
                    break;
                case '4':
                    $descuento = '2';
                    break;
                case '6':
                    $descuento = '2';
                    break;
                default:
                    $descuento = '0';
                    break;
            }
            $tarifaInscripcion = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '1'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $tarifaMensual = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '2'],
                ['tipo_estudiante', $descuento]
            ])->first();

            if ($inscripcion->tipo_estudiante == '2') {
                $tarifaInscripcion->importe = $tarifaInscripcion->importe / 2;
            }

            $pagoActual = InscripcionPago::where([["inscripciones_id", $inscripcion->id], ["concepto_pagos_id", "!=", 3]])->sum("monto");
            $mora = InscripcionPago::where([["inscripciones_id", $inscripcion->id], ["concepto_pagos_id", 3]])->sum("monto");
            // dd($mora);
            // 400  400-250 = 150
            $resto = $pagoActual;
            for ($i = 0; $i < 5; $i++) {
                if ($i == 0) {
                    // $monto = $resto;

                    $tarifaEstudiante = new TarifaEstudiante();
                    $tarifaEstudiante->monto = $tarifaInscripcion->importe;
                    if ($resto == 0) {

                        $tarifaEstudiante->pagado = 0;
                    } else if ($resto > 0 && $resto - $tarifaInscripcion->importe < 0) {
                        $tarifaEstudiante->pagado =  $resto;
                        $resto = 0;
                    } else {
                        $tarifaEstudiante->pagado =  $tarifaInscripcion->importe;
                        $resto = $resto - $tarifaInscripcion->importe;
                    }
                    $tarifaEstudiante->nro_cuota = $i;
                    $tarifaEstudiante->tipo_estudiante = $inscripcion->tipo_estudiante;
                    $tarifaEstudiante->modalidad = $inscripcion->modalidad;
                    $tarifaEstudiante->estudiantes_id = $inscripcion->estudiantes_id;
                    $tarifaEstudiante->save();
                } else {
                    $tarifaEstudiante = new TarifaEstudiante();
                    $tarifaEstudiante->monto = $tarifaMensual->importe;
                    if ($resto == 0) {

                        $tarifaEstudiante->pagado = 0;
                    } else if ($resto > 0 && $resto - $tarifaMensual->importe < 0) {
                        $tarifaEstudiante->pagado =  $resto;
                        $resto = 0;
                    } else {
                        $tarifaEstudiante->pagado =  $tarifaMensual->importe;
                        $resto = $resto - $tarifaMensual->importe;
                    }
                    $tarifaEstudiante->nro_cuota = $i;
                    $tarifaEstudiante->tipo_estudiante = $inscripcion->tipo_estudiante;
                    $tarifaEstudiante->modalidad = $inscripcion->modalidad;
                    $tarifaEstudiante->estudiantes_id = $inscripcion->estudiantes_id;
                    $tarifaEstudiante->save();
                }
            }
            $tarifaMora = TarifaEstudiante::where([["estudiantes_id", $inscripcion->estudiantes_id], ["nro_cuota", 1]])->first();
            $tarifaMora->mora = $mora;
            $tarifaMora->save();
        }
    });


    Route::get('/matricular-estudiante-curso', function () {
        $apiGsuite = new GWorkspace();

        $datos = json_encode(array(
            "courseId" => '435435657968',
            "userId" => '112935238301898015775'
        ));

        $matricular = json_decode($apiGsuite->matricularEstudiante($datos));
        if ($matricular->success) {
            $status = true;
            $response["message"] =  'Success.';
        } else {
            $status = false;
            $response["message"] =  'Error.';
        }
        dd($response, $status);
    });
    Route::get('/crear-curso', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "name" => "CURSO PRUEBA1",
            "section" => "GRUPO A",
            "room" => "INGENIERIAS [GRUPO A]",
        ));
        $cursoCreado = json_decode($apiGsuite->crearCurso($datos));
        dd($cursoCreado);
    });
    Route::get('/correosi/{correo}', function ($correo) {

        $correoDocente = new GWorkspace();
        $datos = json_encode(array(
            // "accountId" => "72768753@cepreuna.edu.pe",
            "accountId" => $correo,
            // "orgUnitPath"  => env('ORGUNITPATH'),
        ));
        $correoGenerado = json_decode($correoDocente->consulta($datos));
        dd($correoGenerado);
    });
    Route::get('/crear', function () {
        dd(env('ORGUNITPATHE'));
    });
    Route::get('/crear-correo', function () {
        $correoEstudiante = new GWorkspace();
        $datos = json_encode(array(
            "primaryEmail" => 'flizarragaj@cepreuna.edu.pe',
            "name_givenName" => 'FIORELA L.',
            "name_familyName" => 'LIZARRAGA JALIRI',
            "orgUnitPath"  => env('ORGUNITPATH'),
            "password"  => '951546473',
            "recoveryEmail"  => 'fiorelalizarragaj@gmail.com',
            "recoveryPhone"  => "",
            "customSchemas_Institution_Document"  => '00000000',
            "customSchemas_Institution_Type"  => "Student"
        ));
        $correoGenerado = json_decode($correoEstudiante->correo($datos));
        dd($correoGenerado);
    });
    Route::get('/crear-correo-docente', function () {
        $correoEstudiante = new GWorkspace();
        $datos = json_encode(array(
            "primaryEmail" => 'jmamanit@cepreuna.edu.pe',
            "name_givenName" => 'JAVIER',
            "name_familyName" => 'MAMANI TERRAZAS',
            "orgUnitPath"  => env('ORGUNITPATHD'),
            "password"  => '159753jk',
            "recoveryEmail"  => 'jmamani@unap.edu.pe',
            "recoveryPhone"  => "",
            "customSchemas_Institution_Document"  => '45417101',
            "customSchemas_Institution_Type"  => "Teacher"
        ));
        $correoGenerado = json_decode($correoEstudiante->correo($datos));
        dd($correoGenerado);
    });
    Route::get('/crear-correo-prueba', function () {
        $correoEstudiante = new GWorkspace();
        $datos = json_encode(array(
            "primaryEmail" => 'test_docente@cepreuna.edu.pe',
            "name_givenName" => 'Docente',
            "name_familyName" => 'Test',
            "orgUnitPath"  => env('ORGUNITPATH'),
            "password"  => 'test_docent3',
            "recoveryEmail"  => 'etipula@unap.edu.pe',
            "recoveryPhone"  => "",
            "customSchemas_Institution_Document"  => '00000000',
            "customSchemas_Institution_Type"  => "Teacher"
        ));
        $correoGenerado = json_decode($correoEstudiante->correo($datos));
        dd($correoGenerado);
    });

    Route::get('/matricular-docente', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "courseId" => '256501538791',
            "userId" => 'wsayma@cepreuna.edu.pe',
        ));
        $correoGenerado = json_decode($apiGsuite->matricularDocente($datos));
        dd($correoGenerado);
    });
    Route::get('/matricular-docenteee', function () {
        $apiGsuite = new GWorkspace();

        $consulta = DB::select("SELECT ca.id,ca.idclassroom,da.idgsuite FROM carga_academicas AS ca JOIN docente_aptos AS da ON da.docentes_id = ca.docentes_id JOIN grupo_aulas AS ga ON ga.id = ca.grupo_aulas_id WHERE ga.turnos_id = 1");
        // dd($consulta);
        foreach ($consulta as $value) {

            $datos = json_encode(array(
                "courseId" => $value->idclassroom,
                "userId" => $value->idgsuite,
            ));
            $correoGenerado = json_decode($apiGsuite->matricularDocente($datos));

            $deco = json_decode($correoGenerado->message);
            if ($correoGenerado->status) {
                # code...
            }
            echo $value->id . ' - ' .
                var_dump($deco->error->message);
        }
    });
    Route::get('/matricular-estudiante', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            // "courseId" => '25652385gf2036',
            // "userId" => '1040837560asd90532672925',
        ));
        $correoGenerado = json_decode($apiGsuite->matricularEstudiante($datos));
        dd($correoGenerado);
    });

    Route::get('/matricular-estudiante/{dni}', function ($dni) {
        $apiGsuite = new GWorkspace();
        $cargas = DB::table('carga_academicas as ca')
            ->select(
                'e.idgsuite',
                'ca.idclassroom'
            )
            ->join('matriculas as m', 'm.grupo_aulas_id', 'ca.grupo_aulas_id')
            ->join('estudiantes as e', 'e.id', 'm.estudiantes_id')
            ->where('e.nro_documento', $dni)
            ->get();
        // dd(json_decode($estudiante));
        foreach ($cargas as $carga) {
            try {
                $datos = json_encode(array(
                    "courseId" => $carga->idclassroom,
                    "userId" => $carga->idgsuite,
                ));
                $matricula = json_decode($apiGsuite->matricularEstudiante($datos));
                if ($matricula->success == true) {
                    echo 'sincronizacion correcta \n';
                } else {
                    echo 'error, este curso ya fue sincronizado \n';
                }
            } catch (Exception $gse) {
                echo 'error de api';
            }
        }
    });
    Route::get('/verificar-matricula', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "courseId" => '256604431436',
            "userId" => 'amamani@cepreuna.edu.pe',
        ));
        $correoGenerado = json_decode($apiGsuite->checkMatricula($datos));
        dd($correoGenerado);
    });
    Route::get('/eliminar-matriculad', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "courseId" => '256636894937',
            "userId" => 'jlhuancollo@cepreuna.edu.pe',
        ));
        $correoGenerado = json_decode($apiGsuite->eliminarMatriculaDocente($datos));
        dd($correoGenerado);
    });
    Route::get('/eliminar-matriculae', function () {
        // '{
        //     "courseId":"255801108225",
        //     "userId":"gapazaq@cepreuna.edu.pe"
        // }'

        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "courseId" => '256561421260',
            "userId" => '74178523@cepreuna.edu.pe',
        ));
        $correoGenerado = json_decode($apiGsuite->eliminarMatriculaEstudiante($datos));
        dd($correoGenerado);
    });
    Route::get('/consultar-matricula', function () {
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "studentId" => 'jlhuancollo@cepreuna.edu.pe',
        ));
        $correoGenerado = json_decode($apiGsuite->consultarMatricula($datos));
        dd($correoGenerado);
    });
    // Route::get('/lista-cursos', function () {
    //     $apiGsuite = new GWorkspace();
    //     $datos = json_encode(array(
    //         "studentId" => '75259884@cepreuna.edu.pe',
    //     ));
    //     $correoGenerado = json_decode($apiGsuite->listaCursos($datos));
    //     dd($correoGenerado);
    // });

    Route::get('/lista-cursos-docente', function () {
        // "teacherId":"vcarbajal@cepreuna.edu.pe"
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "teacherId" => 'fmamani@cepreuna.edu.pe',
        ));
        $response = json_decode($apiGsuite->listarCursosDocente($datos));
        dd($response);
    });
    Route::get('/lista-cursos-estudiante', function () {
        // "teacherId":"vcarbajal@cepreuna.edu.pe"
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "studentId" => '73647977@cepreuna.edu.pe',
        ));
        $response = json_decode($apiGsuite->listarCursosEstudiante($datos));
        dd($response);
    });
    Route::get('/cambiar-contrasena', function (Request $request) {
        // ?email=75573642&password=nUmD6XoW&personalEmail=jhoelmostajosupo@gmail.com
        // dd($request->email);
        // "teacherId":"vcarbajal@cepreuna.edu.pe"
        $apiGsuite = new GWorkspace();
        $datos = json_encode(array(
            "email" => $request->email,
            "password" => $request->password,
            "personalEmail" => $request->personalEmail
        ));
        $response = json_decode($apiGsuite->cambiarContrasena($datos));
        dd($response);
        // $apiGsuite = new GWorkspace();
        // $datos = json_encode(array(
        //     "email" => "sociales@cepreuna.edu.pe",
        //     "password" => "sociales2021",
        //     "personalEmail" => "ejtipulaticona@cepreuna.edu.pe"
        // ));
        // $response = json_decode($apiGsuite->cambiarContrasena($datos));
        // dd($response);
    });
});

Route::get('/home', 'HomeController@index')->name('home');



Route::post('/upload', 'ImagenController@upload')->name('upload');
Route::group(['prefix' => 'inscripciones'], function () {
    Route::get('/consultar', 'Web\Inscripcion\IndexController@consultarInscripcion');
    Route::get('/get-local-estudiante/{dni}', 'Web\Inscripcion\IndexController@getLocalEstudiante');

    Route::get('/', 'Web\Inscripcion\IndexController@index');
    Route::resource('/estudiantes', 'Web\Inscripcion\EstudianteController');
    Route::resource('/docentes', 'Web\Inscripcion\DocenteController');
    /*Simulacro*/
    Route::resource('/simulacro', 'Web\Inscripcion\SimulacroController');
    Route::get('validacion', 'Web\Inscripcion\SimulacroController@showLogin');
    Route::post('validacion', 'Web\Inscripcion\SimulacroController@login')->name('loginSimulacro');
    /**/
    /*Disponibilidad*/
    Route::resource('/disponibilidades', 'Web\Inscripcion\DisponibilidadController');
    Route::get('disponibilidad', 'Web\Inscripcion\DisponibilidadController@showLogin');
    Route::post('disponibilidad', 'Web\Inscripcion\DisponibilidadController@login')->name('logindisponibilidad');
    /**/
    Route::get('/get-paises', 'Web\Inscripcion\IndexController@getPaises');
    Route::get('/get-departamentos', 'Web\Inscripcion\IndexController@getDepartamentos');
    Route::get('/get-provincias', 'Web\Inscripcion\IndexController@getProvincias');
    Route::get('/get-distritos', 'Web\Inscripcion\IndexController@getDistritos');
    Route::get('/get-colegios', 'Web\Inscripcion\IndexController@getColegios');
    Route::get('/get-sedes', 'Web\Inscripcion\IndexController@getSedes');
    Route::get('/get-sedes-modalidad', 'Web\Inscripcion\IndexController@getSedesModalidad');
    Route::get('/get-areas', 'Web\Inscripcion\IndexController@getAreas');
    Route::get('/get-vacante-areas', 'Web\Inscripcion\IndexController@getVacanteAreas');
    Route::get('/get-vacante-turnos', 'Web\Inscripcion\IndexController@getVacanteTurnos');
    Route::get('/get-vacante-modalidad', 'Web\Inscripcion\IndexController@getVacanteModalidad');
    Route::get('/get-vacante-sedes', 'Web\Inscripcion\IndexController@getVacanteSede');
    Route::get('/get-vacantes-agotadas', 'Web\Inscripcion\IndexController@getVacantesAgotadas');

    Route::get('/get-estado-vacantes', 'Web\Inscripcion\IndexController@getEstadoVacantes');
    Route::get('/get-turnos', 'Web\Inscripcion\IndexController@getTurnos');
    Route::get('/get-escuelas', 'Web\Inscripcion\IndexController@getEscuelas');
    Route::get('/get-parentescos', 'Web\Inscripcion\IndexController@getParentescos');
    Route::get('/get-tipo-documentos', 'Web\Inscripcion\IndexController@getTipoDocumentos');
    Route::get('/get-plantilla-horario', 'Web\Inscripcion\IndexController@getPlantillaHorario');
    Route::get('/get-cursos', 'Web\Inscripcion\IndexController@getCursos');
    Route::get('/get-grados-academicos', 'Web\Inscripcion\IndexController@getGradosAcademicos');
    Route::get('/get-programas', 'Web\Inscripcion\IndexController@getProgramas');
    Route::get('/get-experiencias', 'Web\Inscripcion\IndexController@getExperiencias');
    Route::get('/get-capacitaciones', 'Web\Inscripcion\IndexController@getcapacitaciones');
    Route::get('/get-producciones', 'Web\Inscripcion\IndexController@getProducciones');
    Route::get('/estudiantes/pdf/{id}', 'Web\Inscripcion\EstudianteController@pdf');
    Route::get('/docentes/pdf/{id}', 'Web\Inscripcion\DocenteController@pdf');

    Route::get('/simulacro/pdf/{id}', 'Web\Inscripcion\SimulacroController@pdf');
});

Route::post('validar-pago/', 'Web\PagoController@validarPago');
Route::post('get-tarifa-colegio/', 'Web\PagoController@getTarifaColegio');
Route::post('get-tarifa-modalidad/', 'Web\PagoController@getTarifaModalidad');

// Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/inscripciones/estudiantes-extemporaneos', 'Web\Inscripcion\EstudianteController@indexExtemporaneo');
Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    Route::group(['prefix' => 'inscripciones'], function () {
        Route::get('/docentes-extemporaneos', 'Web\Inscripcion\DocenteController@indexExtemporaneo');
        Route::get('/download-pdf', 'Web\Inscripcion\DocenteController@download_pdf');
        // Route::get('/estudiantes-extemporaneos', 'Web\Inscripcion\EstudianteController@indexExtemporaneo');
        Route::get('/download-pdf', 'Web\Inscripcion\DocenteController@download_pdf');
    });

    Route::group(['prefix' => 'intranet'], function () {

        Route::get('/get-paises', 'Intranet\IndexController@getPaises');
        Route::get('/get-departamentos', 'Intranet\IndexController@getDepartamentos');
        Route::get('/get-provincias', 'Intranet\IndexController@getProvincias');
        Route::get('/get-distritos', 'Intranet\IndexController@getDistritos');
        Route::get('/get-colegios', 'Intranet\IndexController@getColegios');
        Route::get('/get-tipo-colegios', 'Intranet\IndexController@getTipoColegios');
        Route::get('/get-conceptos', 'Intranet\IndexController@getConceptos');
        Route::get('/get-sedes', 'Intranet\IndexController@getSedes');
        Route::get('/get-areas', 'Intranet\IndexController@getAreas');
        Route::get('/get-vacante-areas', 'Intranet\IndexController@getVacanteAreas');
        Route::get('/get-vacante-turnos', 'Intranet\IndexController@getVacanteTurnos');
        Route::get('/get-estado-vacantes', 'Intranet\IndexController@getEstadoVacantes');
        Route::get('/get-turnos', 'Intranet\IndexController@getTurnos');
        Route::get('/get-parentescos', 'Intranet\IndexController@getParentescos');
        Route::get('/get-tipo-documentos', 'Intranet\IndexController@getTipoDocumentos');
        Route::get('/get-plantilla-horario', 'Intranet\IndexController@getPlantillaHorario');
        Route::get('/get-cursos', 'Intranet\IndexController@getCursos');
        Route::get('/get-grados-academicos', 'Intranet\IndexController@getGradosAcademicos');
        Route::get('/get-programas', 'Intranet\IndexController@getProgramas');
        Route::get('/get-locales', 'Intranet\IndexController@getLocales');
        Route::get('/get-grupos', 'Intranet\IndexController@getGrupos');
        Route::get('/get-only-grupos', 'Intranet\IndexController@getOnlyGrupos');
        Route::get('/get-only-aulas', 'Intranet\IndexController@getOnlyAulas');
        Route::get('/get-grupo-aulas', 'Intranet\IndexController@getGrupoAula');
        Route::get('/get-grupo-aulas-aux', 'Intranet\IndexController@getGrupoAulaAux');
        Route::get('/get-docentes', 'Intranet\IndexController@getDocente');
        Route::get('/get-disponibilidad', 'Intranet\IndexController@getDisponibilidad');
        Route::get('/get-carga-academica', 'Intranet\IndexController@getCargaAcademica');
        Route::get('/get-asistencia-estudiante', 'Intranet\IndexController@getAsistenciaEstudiante');
        Route::get('/get-asistencia-estudiante-excel/{id}', 'Intranet\IndexController@getAsistenciaEstudianteExcel');
        Route::get('/get-lista-estudiante-excel/{id}', 'Intranet\IndexController@getListaEstudianteExcel');
        Route::get('/get-asistencia-docente', 'Intranet\IndexController@getAsistenciaDocente');
        Route::get('/get-asistencia-docente-excel', 'Intranet\IndexController@getAsistenciaDocenteExcel');
        Route::get('/get-criterio-docente-inscripcion/{id}', 'Intranet\IndexController@getCriterioDocenteInscripcion');
        Route::get('/get-inscripcion-file/{id}', 'Intranet\IndexController@getInscripcionFile');

        Route::get('/get-carga-academica-asistencia', 'Intranet\IndexController@getCargaAcademicaAsistencia');
        Route::get('/get-carga-academica-regularizar', 'Intranet\IndexController@getCargaAcademicaRegularizar');
        Route::get('/get-grupo-aulas-auxiliar', 'Intranet\IndexController@getGrupoAulaAuxiliar');
        Route::get('/get-sesiones', 'Intranet\IndexController@getSesiones');

        Route::get('/get-rol', 'Intranet\IndexController@getRol');
        Route::get('/get-roles', 'Intranet\IndexController@getRoles');
        Route::get('/get-permisos', 'Intranet\IndexController@getPermisos');
        Route::get('/get-cursos-curricula', 'Intranet\IndexController@getCursosCurricula');

        Route::get('/get-docente-cursos', 'Intranet\IndexController@getDocenteCursos');
        Route::get('/get-docente-disponibilidad', 'Intranet\IndexController@getDocenteDisponibilidad');

        Route::get('/get-carga-academica-estudiante/{id}', 'Intranet\IndexController@getCargaAcademicaEstudiante');

        Route::get('/get-curricula-detalle/{id}', 'Intranet\IndexController@getCurriculaDetalle');

        Route::get('/get-propuesta-horario', 'Intranet\IndexController@getPropuestaHorario');

        Route::post('/eliminar-correo/{id}', 'Intranet\IndexController@eliminarCorreo');
        Route::get('/get-documentos', 'Intranet\IndexController@getDocumentos');
        Route::get('/get-meses', 'Intranet\IndexController@getMeses');

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', 'Intranet\DashboardController@index')->name('dashboard');
            Route::get('/get-cantidad', 'Intranet\DashboardController@getCantidad');
            Route::get('/get-cantidad-matriculados', 'Intranet\DashboardController@getCantidadMatriculados');
            Route::get('/script', 'Intranet\DashboardController@script');
        });


        Route::get('/encrypt/{id}', 'Intranet\DashboardController@encrypt');

        Route::group(['prefix' => 'inscripcion'], function () {
            Route::resource('/estudiante', 'Intranet\Inscripcion\EstudianteController');
            Route::get('estudiante/lista/data', 'Intranet\Inscripcion\EstudianteController@lista');
            Route::delete('estudiante/retirar/{id}', 'Intranet\Inscripcion\EstudianteController@retirar');
            // Route::get('estudiante/lista/data', 'Intranet\Inscripcion\EstudianteController@lista');
            Route::get('/estudiantes/pdf/{id}', 'Web\Inscripcion\EstudianteController@pdf');
            Route::get('/estudiantes/pdf-constancia/{id}', 'Web\Inscripcion\EstudianteController@pdfConstancia');

            Route::post('/agregar-pago', 'Intranet\Inscripcion\EstudianteController@agregarPago');
            Route::post('/agregar-pago-mora', 'Intranet\Inscripcion\EstudianteController@agregarPagoMora');
            Route::post('/eliminar-voucher/{id}', 'Intranet\Inscripcion\EstudianteController@eliminarVoucher');


            Route::resource('/docente', 'Intranet\Inscripcion\DocenteController');
            Route::get('docente/lista/data', 'Intranet\Inscripcion\DocenteController@lista');
            Route::post('docente/calificar/{id}', 'Intranet\Inscripcion\DocenteController@calificar');
            Route::post('docente/cursos/{id}', 'Intranet\Inscripcion\DocenteController@cursos');
            Route::post('docente/disponibilidad/{id}', 'Intranet\Inscripcion\DocenteController@disponibilidad');

            Route::get('/docentes/pdf/{id}', 'Web\Inscripcion\DocenteController@pdf');

            Route::resource('/calificacion-docente', 'Intranet\Inscripcion\CalificacionDocenteController');
            Route::get('/calificacion-docente/lista/data', 'Intranet\Inscripcion\CalificacionDocenteController@lista');

            Route::get('docente/enviar-accesos/{id}', 'Intranet\Inscripcion\DocenteController@enviarAccesos');
        });
        Route::group(['prefix' => 'matricula'], function () {
            Route::get('/pendiente', 'Intranet\MatriculaController@pendiente');
            Route::get('/pendiente/lista/data', 'Intranet\MatriculaController@lista');
            Route::post('/guardar', 'Intranet\MatriculaController@store');

            Route::get('/matriculado', 'Intranet\MatriculaController@matriculado');
            Route::get('/matriculado/lista/data', 'Intranet\MatriculaController@lista_matriculado');

            Route::delete('/eliminar/{id}', 'Intranet\MatriculaController@delete');
            Route::put('/vincular/{id}', 'Intranet\MatriculaController@vincular');
            Route::put('/bloquear-panel/{id}', 'Intranet\MatriculaController@bloquearPanel');
            Route::put('/desbloquear-panel/{id}', 'Intranet\MatriculaController@desbloquearPanel');

            Route::get('/habilitar', 'Intranet\MatriculaController@habilitar');
            Route::get('/habilitar/lista/data', 'Intranet\MatriculaController@lista_habilitar');
            Route::put('/habilitar-edicion/{id}', 'Intranet\MatriculaController@habilitarEdicion');
            Route::put('/deshabilitar-edicion/{id}', 'Intranet\MatriculaController@deshabilitarEdicion');
        });
        Route::group(['prefix' => 'asistencia'], function () {
            Route::resource('/docente', 'Intranet\Asistencia\DocenteController');
            Route::get('/docente/lista/data', 'Intranet\Asistencia\DocenteController@lista');
        });
        Route::group(['prefix' => 'administracion'], function () {
            Route::resource('/horario', 'Intranet\Administracion\HorarioController');
            Route::put('/horario/desmatricular/docente', 'Intranet\Administracion\HorarioController@desmastricular');
            Route::put('/horario/vincular/docente', 'Intranet\Administracion\HorarioController@vincular');

            Route::resource('/propuesta-horario', 'Intranet\Administracion\PropuestaHorarioController');

            Route::resource('/grupo-aula', 'Intranet\Administracion\GrupoAulaController');
            Route::get('/grupo-aula/lista/data', 'Intranet\Administracion\GrupoAulaController@lista');

            Route::resource('/vacantes', 'Intranet\Administracion\VacanteController');
            Route::get('/vacantes/lista/data', 'Intranet\Administracion\VacanteController@lista');

            Route::resource('/cronograma-pagos', 'Intranet\Administracion\CronogramaPagosController');
            Route::get('/cronograma-pagos/lista/data', 'Intranet\Administracion\CronogramaPagosController@lista');

            Route::resource('/tarifario-estudiante', 'Intranet\Administracion\TarifarioEstudianteController');
            Route::get('/tarifario-estudiante/lista/data', 'Intranet\Administracion\TarifarioEstudianteController@lista');

            Route::resource('/horario-inscripcion', 'Intranet\Administracion\HorarioInscripcionController');

            // Route::group(['prefix' => 'docente'], function() {
            Route::resource('/docente', 'Intranet\Administracion\DocenteController');
            Route::get('docente/lista/data', 'Intranet\Administracion\DocenteController@lista');
            // });
            Route::resource('/estudiante', 'Intranet\Administracion\EstudianteController');
            Route::get('/estudiante/lista/data', 'Intranet\Administracion\EstudianteController@lista');
            Route::get('/actualizar-foto', 'Intranet\Administracion\EstudianteController@indexFoto');
            Route::post('/update-image', 'Intranet\Administracion\EstudianteController@updateImage');
            Route::post('/estudiante/activar-gsuite/{id}', 'Intranet\Administracion\EstudianteController@activarGsuite');

            Route::resource('/auxiliar', 'Intranet\Administracion\AuxiliarController');
            Route::get('auxiliar/lista/data', 'Intranet\Administracion\AuxiliarController@lista');
            Route::post('/auxiliar-grupo/{id}', 'Intranet\Administracion\AuxiliarController@AsignarGrupos');
            Route::get('/auxiliar-grupo/{id}/edit', 'Intranet\Administracion\AuxiliarController@GruposAsignados');

            Route::resource('/coordinador-auxiliar', 'Intranet\Administracion\CoordinadorAuxiliarController');
            Route::get('coordinador-auxiliar/lista/data', 'Intranet\Administracion\CoordinadorAuxiliarController@lista');
            Route::get('coordinador-auxiliar-auxiliar/lista/data', 'Intranet\Administracion\CoordinadorAuxiliarController@lista_auxiliar');
            Route::post('/coordinador-grupo/{id}', 'Intranet\Administracion\CoordinadorAuxiliarController@AsignarGrupos');
            Route::get('/coordinador-grupo/{id}/edit', 'Intranet\Administracion\CoordinadorAuxiliarController@GruposAsignados');
            Route::resource('/inscripciones', 'Intranet\Configuracion\InscripcionesController');
            Route::get('inscripciones/lista/data', 'Intranet\Configuracion\InscripcionesController@lista');

            // crons
            Route::get('cron-matricula/', 'Intranet\Administracion\ControlCronController@indexCronMatricula');
            Route::get('control-matricula/', 'Intranet\Administracion\ControlCronController@controlMatricula');
            Route::get('sincronizar-matricula/', function () {
                Artisan::call('matricula:task');
            });
            Route::get('cron-encuesta/', 'Intranet\Administracion\ControlCronController@indexCronEncuesta');
            Route::get('control-encuesta/', 'Intranet\Administracion\ControlCronController@controlEncuesta');
            Route::post('estado-encuesta-cambiar', function (Request $request) {
                Artisan::call('encuesta:task ' . $request->input('estado', 1));
            });

            Route::get('cron-grupo/', 'Intranet\Administracion\ControlCronController@indexCronGrupo');
            Route::get('control-grupo/', 'Intranet\Administracion\ControlCronController@controlGrupo');
            Route::get('sincronizar-grupo/', function () {
                Artisan::call('grupo:task');
            });

            Route::get('cron-habilitacion/', 'Intranet\Administracion\ControlCronController@indexCronHabilitacion');
            Route::get('control-habilitacion/', 'Intranet\Administracion\ControlCronController@controlHabilitacion');
            Route::get('sincronizar-habilitacion/', function () {
                Artisan::call('habilitacion:task');
            });

            Route::get('cron-docente/', 'Intranet\Administracion\ControlCronController@indexCronDocente');
            Route::get('control-docente/', 'Intranet\Administracion\ControlCronController@controlDocente');
            Route::get('sincronizar-docente/', function () {
                Artisan::call('docente:task');
            });

            Route::get('cron-correo/', 'Intranet\Administracion\ControlCronController@indexCronCorreo');
            Route::get('control-correo/', 'Intranet\Administracion\ControlCronController@controlCorreo');
            Route::get('sincronizar-correo/', function () {
                Artisan::call('correo:task');
            });

        });
        Route::group(['prefix' => 'configuracion'], function () {
            Route::resource('/sede', 'Intranet\Configuracion\SedeController');
            Route::get('sede/lista/data', 'Intranet\Configuracion\SedeController@lista');

            Route::resource('/local', 'Intranet\Configuracion\LocalController');
            Route::get('local/lista/data', 'Intranet\Configuracion\LocalController@lista');
            Route::post('local/upload', 'Intranet\Configuracion\LocalController@upload');

            Route::resource('/aulas', 'Intranet\Configuracion\AulaController');
            Route::get('aulas/lista/data', 'Intranet\Configuracion\AulaController@lista');

            Route::resource('/grupos', 'Intranet\Configuracion\GrupoController');
            Route::get('grupos/lista/data', 'Intranet\Configuracion\GrupoController@lista');

            Route::resource('/criterios', 'Intranet\Configuracion\CriteriosController');
            Route::get('criterios/lista/data', 'Intranet\Configuracion\CriteriosController@lista');

            Route::resource('/tarifas', 'Intranet\Configuracion\TarifasController');
            Route::get('tarifas/lista/data', 'Intranet\Configuracion\TarifasController@lista');

            Route::resource('/areas', 'Intranet\Configuracion\AreasController');
            Route::get('areas/lista/data', 'Intranet\Configuracion\AreasController@lista');

            Route::resource('/grado_academico', 'Intranet\Configuracion\GradoAcademicoController');
            Route::get('grado_academico/lista/data', 'Intranet\Configuracion\GradoAcademicoController@lista');

            Route::resource('/plantilla_horario', 'Intranet\Configuracion\PlantillaHorarioController');
            Route::get('plantilla_horario/lista/data', 'Intranet\Configuracion\PlantillaHorarioController@lista');

            Route::resource('/curricula', 'Intranet\Configuracion\CurriculaController');
            Route::get('curricula/lista/data', 'Intranet\Configuracion\CurriculaController@lista');
            Route::get('curricula/detalle/lista/data', 'Intranet\Configuracion\CurriculaController@detalle');
            Route::post('curricula/detalle/guardar/{id}', 'Intranet\Configuracion\CurriculaController@storeD');
            Route::put('curricula/detalle/editar/{id}', 'Intranet\Configuracion\CurriculaController@updateD');
            Route::delete('curricula/detalle/eliminar/{id}', 'Intranet\Configuracion\CurriculaController@destroyD');

            Route::resource('/turnos', 'Intranet\Configuracion\TurnoController');
            Route::get('turnos/lista/data', 'Intranet\Configuracion\TurnoController@lista');

            Route::resource('/parentescos', 'Intranet\Configuracion\ParentescoController');
            Route::get('parentescos/lista/data', 'Intranet\Configuracion\ParentescoController@lista');

            Route::resource('/programas', 'Intranet\Configuracion\ProgramaController');
            Route::get('programas/lista/data', 'Intranet\Configuracion\ProgramaController@lista');

            Route::resource('/tipo-documento', 'Intranet\Configuracion\TipoDocumentoController');
            Route::get('tipo-documento/lista/data', 'Intranet\Configuracion\TipoDocumentoController@lista');

            Route::resource('/periodos', 'Intranet\Configuracion\PeriodoController');
            Route::get('periodos/lista/data', 'Intranet\Configuracion\PeriodoController@lista');

            Route::resource('/cursos', 'Intranet\Configuracion\CursoController');
            Route::get('cursos/lista/data', 'Intranet\Configuracion\CursoController@lista');

            Route::resource('/colegios', 'Intranet\Configuracion\ColegioController');
            Route::get('colegios/lista/data', 'Intranet\Configuracion\ColegioController@lista');
        });

        // Route::group(['prefix' => 'aplicativo'], function () {
        //     Route::resource('/comunicado', 'Intranet\Aplicativo\ComunicadoController');
        //     Route::get('comunicado/lista/data', 'Intranet\Aplicativo\ComunicadoController@lista');
        // });


        // ****************************
        // *        AUXILIARES
        // ****************************
        Route::group(['prefix' => 'auxiliar'], function () {
            Route::get('habilitacion', 'Intranet\Auxiliar\HabilitacionController@index');
            Route::get('habilitacion/lista/data', 'Intranet\Auxiliar\HabilitacionController@lista');
            Route::put('habilitacion/habilitar/{id}', 'Intranet\Auxiliar\HabilitacionController@habilitar');
            Route::put('habilitacion/deshabilitar/{id}', 'Intranet\Auxiliar\HabilitacionController@deshabilitar');
            Route::get('habilitacion/estudiante/{documento}', 'Intranet\Auxiliar\HabilitacionController@show');
            Route::get('horario', 'Intranet\Auxiliar\HorarioController@index');

            Route::group(['prefix' => 'asistencia'], function () {
                Route::get('/docente', 'Intranet\Auxiliar\AsistenciaDocenteController@index');
                Route::post('/docente', 'Intranet\Auxiliar\AsistenciaDocenteController@store');
                Route::post('/docente/regularizar', 'Intranet\Auxiliar\AsistenciaDocenteController@regularizar');

                Route::get('/estudiante', 'Intranet\Auxiliar\AsistenciaEstudianteController@index');
                Route::post('/estudiante', 'Intranet\Auxiliar\AsistenciaEstudianteController@store');
            });
        });
        // ****************************
        // *        CONRDINADOR AUXILIAR
        // ****************************
        Route::group(['prefix' => 'coordinador-auxiliar'], function () {

            Route::group(['prefix' => 'asistencia'], function () {
                Route::get('/docente', 'Intranet\CoordinadorAuxiliar\AsistenciaDocenteController@index');
                Route::post('/docente', 'Intranet\CoordinadorAuxiliar\AsistenciaDocenteController@store');
                Route::post('/docente/{id}', 'Intranet\CoordinadorAuxiliar\AsistenciaDocenteController@update');

                Route::get('/estudiante', 'Intranet\CoordinadorAuxiliar\AsistenciaEstudianteController@index');
                Route::post('/estudiante', 'Intranet\CoordinadorAuxiliar\AsistenciaEstudianteController@store');
                Route::put('/estudiante/{id}', 'Intranet\CoordinadorAuxiliar\AsistenciaEstudianteController@update');
            });
            Route::group(['prefix' => 'docente'], function () {
                Route::get('/horario', 'Intranet\CoordinadorAuxiliar\HorarioController@index');
                Route::post('/horario', 'Intranet\CoordinadorAuxiliar\HorarioController@store');
                Route::put('/horario/deshabilitar/{id}', 'Intranet\CoordinadorAuxiliar\HorarioController@deshabilitar');
                Route::put('/horario/habilitar/{id}', 'Intranet\CoordinadorAuxiliar\HorarioController@habilitar');
            });
        });

        // ****************************
        // *  COORDINADORES-DOCENTE
        // ****************************
        Route::group(['prefix' => 'coordinador-docente'], function () {
            Route::resource('/cuadernillo', 'Intranet\CoordinadorDocente\CuadernilloController');
            Route::post('/update-cuadernillo/{id}', 'Intranet\CoordinadorDocente\CuadernilloController@updateCuadernillo');
            Route::get('cuadernillo/lista/data', 'Intranet\CoordinadorDocente\CuadernilloController@lista');

            Route::resource('/temario', 'Intranet\CoordinadorDocente\TemarioController');
            Route::post('/update-temario/{id}', 'Intranet\CoordinadorDocente\TemarioController@updateTemario');
            Route::get('temario/lista/data', 'Intranet\CoordinadorDocente\TemarioController@lista');
        });
        // ****************************
        // *  Usuarios y Roles
        // ****************************

        Route::group(['prefix' => 'administrar-usuarios'], function () {
            Route::resource('/usuario', 'Intranet\AdministrarUsuario\UsuarioController');
            Route::put('/usuario/rol/{id}', 'Intranet\AdministrarUsuario\UsuarioController@asignarRolStore');
            Route::get('usuario/lista/data', 'Intranet\AdministrarUsuario\UsuarioController@lista');
            Route::get('usuario/rol/{id}', 'Intranet\AdministrarUsuario\UsuarioController@getRolUser');

            Route::resource('/permisos', 'Intranet\AdministrarUsuario\PermisoController');
            Route::get('permisos/lista/data', 'Intranet\AdministrarUsuario\PermisoController@lista');

            Route::resource('/roles', 'Intranet\AdministrarUsuario\RolController');
            Route::get('roles/lista/data', 'Intranet\AdministrarUsuario\RolController@lista');
        });

        // ****************************
        // *  Recursos Humanos
        // ****************************

        Route::group(['prefix' => 'recursos-humanos'], function () {
            Route::group(['prefix' => 'pagos'], function () {

                Route::get('/docentes/expedientes', 'Intranet\RecursosHumanos\Pagos\DocenteController@index');
                Route::get('/docentes/expedientes/lista/data', 'Intranet\RecursosHumanos\Pagos\DocenteController@lista');
                Route::get('/get-documentos-expediente-docente/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@getDocumentosExpedienteDocente');
                Route::post('/docentes/evaluar-docente', 'Intranet\RecursosHumanos\Pagos\DocenteController@evaluarDocente');

                Route::get('/docentes/habilitacion', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@index');
                Route::post('/docentes/habilitacion/guardar', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@store');
                Route::get('/docentes/habilitacion/lista/data', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@lista');
                Route::post('/docentes/habilitacion/deshabilitar', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@deshabilitar');

                Route::get('/docentes/horas-mes', 'Intranet\RecursosHumanos\Pagos\DocenteController@indexHorasMes');
                Route::get('/docentes/horas-mes/lista/data', 'Intranet\RecursosHumanos\Pagos\DocenteController@listaHorasMes');
                Route::get('/get-periodos', 'Intranet\RecursosHumanos\Pagos\DocenteController@getPeriodos');
                Route::get('/get-mes-hora-docente/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@getMesHorasPeriodo');
                Route::post('/docentes/horas-mes', 'Intranet\RecursosHumanos\Pagos\DocenteController@storeHorasDocente');
                Route::put('/docentes/horas-mes/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@updateHorasDocente');
                Route::get('/get-periodos-filter', 'Intranet\RecursosHumanos\Pagos\DocenteController@getPeriodosFilter');
                Route::get('/get-responsables', 'Intranet\RecursosHumanos\Pagos\DocenteController@getResponsables');
                Route::get('/expedientes-excel', 'Intranet\RecursosHumanos\Pagos\DocenteController@rptExpedientesExcel');
            });
            Route::get('cron-correo/', 'Intranet\RecursosHumanos\ControlCronController@indexCronCorreo');
            Route::get('control-correo/', 'Intranet\RecursosHumanos\ControlCronController@controlCorreo');
            Route::get('sincronizar-correo/', function () {
                Artisan::call('accesos_rh:task');
            });
        });



        // ****************************
        // *  Devoluciones
        // ****************************

        Route::group(['prefix' => 'devolucion'], function () {
            Route::resource('/devoluciones', 'Intranet\RecursosHumanos\Devoluciones\DevolucionesController');
            Route::get('/devoluciones/lista/data', 'Intranet\RecursosHumanos\Devoluciones\DevolucionesController@lista');
            Route::get('/devoluciones/{id}', 'Intranet\RecursosHumanos\Devoluciones\DevolucionesController@edit');
            Route::get('/get-documentos-expediente-docente/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@getDocumentosExpedienteDocente');
            Route::post('/docentes/evaluar-docente', 'Intranet\RecursosHumanos\Pagos\DocenteController@evaluarDocente');

            Route::get('/docentes/habilitacion', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@index');
            Route::post('/docentes/habilitacion/guardar', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@store');
            Route::get('/docentes/habilitacion/lista/data', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@lista');
            Route::post('/docentes/habilitacion/deshabilitar', 'Intranet\RecursosHumanos\Pagos\HabilitacionDocenteController@deshabilitar');

            Route::get('/docentes/horas-mes', 'Intranet\RecursosHumanos\Pagos\DocenteController@indexHorasMes');
            Route::get('/docentes/horas-mes/lista/data', 'Intranet\RecursosHumanos\Pagos\DocenteController@listaHorasMes');
            Route::get('/get-periodos', 'Intranet\RecursosHumanos\Pagos\DocenteController@getPeriodos');
            Route::get('/get-mes-hora-docente/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@getMesHorasPeriodo');
            Route::post('/docentes/horas-mes', 'Intranet\RecursosHumanos\Pagos\DocenteController@storeHorasDocente');
            Route::put('/docentes/horas-mes/{id}', 'Intranet\RecursosHumanos\Pagos\DocenteController@updateHorasDocente');
            Route::get('/get-periodos-filter', 'Intranet\RecursosHumanos\Pagos\DocenteController@getPeriodosFilter');
            Route::get('/get-responsables', 'Intranet\RecursosHumanos\Pagos\DocenteController@getResponsables');
            Route::get('/expedientes-excel', 'Intranet\RecursosHumanos\Pagos\DocenteController@rptExpedientesExcel');
        });


        // ****************************
        // *  Libro de Reclamaciones
        // ****************************
        Route::group(['prefix' => 'libro-reclamaciones'], function () {
            Route::get('/reclamaciones', 'Intranet\LibroReclamaciones\LibroReclamacionesController@index');
            Route::get('/lista/data', 'Intranet\LibroReclamaciones\LibroReclamacionesController@lista');
            Route::get('/get-datos-reclamo/{id}', 'Intranet\LibroReclamaciones\LibroReclamacionesController@getDatosReclamo');
            Route::get('/get-info-reclamo/{id}', 'Intranet\LibroReclamaciones\LibroReclamacionesController@getInfoReclamo');
            Route::post('/responder-reclamo', 'Intranet\LibroReclamaciones\LibroReclamacionesController@responderReclamo');
        });

        // ****************************
        // *  Nosotros
        // ****************************
        Route::group(['prefix' => 'administrar-nosotros'], function () {
            Route::resource('/directivos', 'Intranet\AdministrarNosotros\DirectivosController');
            Route::get('directivos/lista/data', 'Intranet\AdministrarNosotros\DirectivosController@lista');
            Route::put('/directivos/desactivar/{id}', 'Intranet\AdministrarNosotros\DirectivosController@desactivar');
            Route::resource('/misionvision', 'Intranet\AdministrarNosotros\MisionVisionController');
            Route::get('misionvision/lista/data', 'Intranet\AdministrarNosotros\MisionVisionController@lista');
            Route::put('/misionvision/desactivar/{id}', 'Intranet\AdministrarNosotros\MisionVisionController@desactivar');
            Route::resource('/objetivos', 'Intranet\AdministrarNosotros\ObjetivosController');
            Route::get('objetivos/lista/data', 'Intranet\AdministrarNosotros\ObjetivosController@lista');
            Route::put('/objetivos/desactivar/{id}', 'Intranet\AdministrarNosotros\ObjetivosController@desactivar');
            Route::resource('/historia', 'Intranet\AdministrarNosotros\HistoriaController');
            Route::get('historia/lista/data', 'Intranet\AdministrarNosotros\HistoriaController@lista');
            Route::put('/historia/desactivar/{id}', 'Intranet\AdministrarNosotros\HistoriaController@desactivar');
            Route::resource('/ciclos', 'Intranet\AdministrarNosotros\CicloController');
            Route::post('/update-ciclos/{id}', 'Intranet\AdministrarNosotros\CicloController@update');
            Route::get('ciclos/lista/data', 'Intranet\AdministrarNosotros\CicloController@lista');
        });

        // ****************************
        // *  Reportes
        // ****************************
        Route::group(['prefix' => 'reporte'], function () {
            Route::get('/estudiante', 'Intranet\ReporteController@rptEstudiantesIndex');
            Route::get('/estudiante/lista', 'Intranet\ReporteController@rptEstudianteLista');
            Route::get('/docente', 'Intranet\ReporteController@rptDocenteIndex');
            Route::get('/docente/lista', 'Intranet\ReporteController@rptDocenteLista');
            Route::get('/docente-horas-total', 'Intranet\ReporteController@rptDocenteHorasTotal')->name('intranet.reporte.docente');
            Route::post('/docente-horas-sede', 'Intranet\ReporteController@rptDocenteSede')->name('intranet.reporte.docenteSede');
            Route::post('/docente-horas-virtual', 'Intranet\ReporteController@rptDocenteVirtual');

            Route::get('/asistencia-docente', 'Intranet\ReporteController@rptAsistenciaDocenteIndex');
            Route::get('/asistencia-docente/lista', 'Intranet\ReporteController@rptAsistenciaDocenteLista');
            Route::get('/asistencia-docente/pdf', 'Intranet\ReporteController@rptAsistenciaDocentePdf');

            Route::get('/docente-calificacion', 'Intranet\ReporteController@rptDocenteCalificacionIndex');
            Route::get('/docente-calificacion/lista', 'Intranet\ReporteController@rptDocenteCalificacionLista');
            Route::get('/docente-calificacion/detalle/{id}', 'Intranet\ReporteController@rptDocenteCalificacionDetalle');

            Route::get('/estudiante-asistencia/pdf/{id}', 'Intranet\ReporteController@rptEstudianteAsistenciaPdf');
            Route::get('/docente-asistencia/pdf/{grupo}/{fecha}', 'Intranet\ReporteController@rpDocenteAsistenciaPdf');
            Route::get('/docente-parte/pdf', 'Intranet\ReporteController@rpDocentePartePdf');
            Route::get('/estudiante-lista/pdf/{id}', 'Intranet\ReporteController@rptEstudianteListaPdf');
            Route::get('/estudiante-carnet/pdf/{id}', 'Intranet\ReporteController@carnetPdf');
            Route::get('/estudiante-carnet-reverso/pdf', 'Intranet\ReporteController@carnetPdfReverso');

            Route::get('/docente-ingresantes', 'Intranet\ReporteController@rptDocenteIngresantesIndex');
            Route::get('/docente-ingresantes/lista', 'Intranet\ReporteController@rptDocenteIngresantesLista');

            Route::get('/vouchers', 'Intranet\ReporteController@rptVouchersIndex');
            Route::get('/vouchers/pdf', 'Intranet\ReporteController@generarPDFVouchers');
        

            // Route::post('/update-cuadernillo/{id}', 'Intranet\CoordinadorDocente\CuadernilloController@updateCuadernillo');
            // Route::get('cuadernillo/lista/data', 'Intranet\CoordinadorDocente\CuadernilloController@lista');

            Route::get('/pagos', 'Intranet\ReporteController@rptPagosIndex');
            Route::get('/pagos/lista', 'Intranet\ReporteController@rptPagosLista');
            Route::get('/pagos/pdf', 'Intranet\ReporteController@rptPagosPdf');
            Route::get('/pagos/lista-excel', 'Intranet\ReporteController@rptPagosListaExcel');
            Route::get('/pagos/efectuados-distincion', 'Intranet\ReporteController@rptPagosEfectuadosDistincion')->name('intranet.reporte.pagos');

            //Route::get('/personalizados', 'Intranet\ReporteController@rptPersonalizado');
            //Route::post('/reportes_personalizados', 'Intranet\ReporteController@generadorRpt')->name('intranet.reporte.generador');
        });
        Route::group(['prefix' => 'estadistica'], function () {
            Route::get('/', 'Intranet\EstadisticaController@index');
            Route::get('/g-1reportes-1grafico', 'Intranet\EstadisticaController@G1Reportes1Grafico');
            Route::get('/g-1reportes-2grafico', 'Intranet\EstadisticaController@G1Reportes2Grafico');
            Route::get('/g-1reportes-3grafico', 'Intranet\EstadisticaController@G1Reportes3Grafico');
            Route::get('/g-2reportes-1grafico', 'Intranet\EstadisticaController@G2Reportes1Grafico');
            Route::get('/g-2reportes-2grafico', 'Intranet\EstadisticaController@G2Reportes2Grafico');
            Route::get('/g-2reportes-3grafico', 'Intranet\EstadisticaController@G2Reportes3Grafico');
            Route::get('/g-3reportes-1grafico', 'Intranet\EstadisticaController@G3Reportes1Grafico');
            Route::get('/g-4reportes-1grafico', 'Intranet\EstadisticaController@G4Reportes1Grafico');
        });
    });
    //Route::resource('products', ProductController::class);
    Route::get('/totalHorasModalidadDocente', 'Intranet\ReporteController@totalHorasModalidadDocente');
});
