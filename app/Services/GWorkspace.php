<?php

namespace App\Services;

class GWorkspace
{
	public static function correo($datos)
	{
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/usersInsert',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
	}
    public static function consulta($datos)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/usersGet',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function crearCurso($datos){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesCreate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function matricularDocente($datos){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesTeachersCreate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function matricularEstudiante($datos)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsCreate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function checkMatricula($datos)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsGet',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$datos,
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
    }
    public static function listaCursos($datos)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsMyCourses',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function eliminarMatriculaEstudiante($datos){
        // '{
        //     "courseId":"255801108225",
        //     "userId":"gapazaq@cepreuna.edu.pe"
        // }'
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsDelete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function eliminarMatriculaDocente($datos){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesTeachersDelete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function consultarMatricula($datos){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsMyCourses',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function listarCursosEstudiante($datos)
    {
        // '{
        //     "studentId":"api_ocho@cepreuna.edu.pe"
        // }'
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesStudentsMyCourses',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$datos,
        CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public static function listarCursosDocente($datos)
    {
        $curl = curl_init();
        // "teacherId":"vcarbajal@cepreuna.edu.pe"
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/coursesTeachersMyCourses',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$datos,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function cambiarContrasena($datos){
        // '{
        //     "email":"api_ocho@cepreuna.edu.pe",
        //     "password":"Demo",
        //     "personalEmail":"gapazaq@gmail.com"
        // }',
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/usersPatch',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$datos,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer demo'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public function eliminarCorreo($data)
    {
        // '{
        //     "userId":"47521697@cepreuna.edu.pe"
        // }'
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sistemas.cepreuna.edu.pe:82/api/usersDelete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer _qSdMWKFiiSJY2zMyxN1H'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
