<?php

namespace App\Http\Controllers\Web\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Models\Periodo;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:estudiante');
    }
    public function index()
    {
        // dd(Auth::user()->usuario);
        return view('web.estudiante.perfil');
    }
    public function getEstudiante()
    {
        $idEstudiante = Auth::user()->id;
        $response['estudiante'] = Estudiante::with('tipoDocumento', 'colegio', 'ubigeo')
            ->select(
                'estudiantes.id',
                'estudiantes.nombres',
                'estudiantes.paterno',
                'estudiantes.materno',
                'estudiantes.nro_documento',
                'estudiantes.tipo_documentos_id',
                'estudiantes.usuario',
                'estudiantes.colegios_id',
                'estudiantes.foto',
                'estudiantes.celular',
                'estudiantes.edit',
                DB::raw("DATE_FORMAT(estudiantes.fecha_nac,'%d/%m/%Y') as fecha_nac"),
                'estudiantes.anio_egreso',
                'estudiantes.ubigeos_id',
                'estudiantes.ubigeos_nacimiento',
                'u.departamento',
                'u.provincia',
                'u.distrito'
            )
            ->leftJoin('ubigeos as u', 'u.id', 'estudiantes.ubigeos_nacimiento')
            ->where('estudiantes.id', $idEstudiante)->first();
        $response["_token"] = Crypt::encryptString($idEstudiante . "-" . $response["estudiante"]->nro_documento);
        return response()->json($response);
    }

    public function confirmarDatos()
    {
        $idEstudiante = Auth::user()->id;
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->edit = "1";
            $estudiante->save();

            DB::commit();
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
        }
        return response()->json($response);
    }
    public function actualizarDatos(Request $request)
    {
        // dd($request->all());
        $rules = $request->validate([
            'nombres' => 'required|regex:/^[a-zA-Z áéíóúÁÉÍÓÚñÑ]+$/',
            'paterno' => 'required|regex:/^[a-zA-Z áéíóúÁÉÍÓÚñÑ]+$/',
            'materno' => 'required|regex:/^[a-zA-Z áéíóúÁÉÍÓÚñÑ]+$/',
            'nro_documento' => 'required|regex:/^[0-9]+$/|size:8',
            'foto' => 'required',
            'fecha_nac' => 'required',
            'pais' => 'required',
            'departamento' => 'required_if:pais,1',
            'provincia' => 'required_if:pais,1',
            'ubigeo' => 'required_if:pais,1',
            'egreso' => 'required|integer|min:1900',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'nombres.regex' => '* Solo se admiten letras.',
            'paterno.regex' => '* Solo se admiten letras.',
            'materno.regex' => '* Solo se admiten letras.',
            'nro_documento.regex' => '* Solo se admiten números.',
            'foto.required' => '* La fotografia es obligatoria.',
            'nro_documento.size' => '* Solo se admiten 8 números.',
            'ubigeo.required_if' => '* El campo distrito es obligatorio.',
            'fecha_nac.required' => '* La fecha de nacimiento es obligatorio',
            'egreso.min' => '* El año ingresado no es valido',
        ]);

        $idEstudiante = Auth::user()->id;
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->nombres = mb_strtoupper($request->nombres);
            $estudiante->paterno = mb_strtoupper($request->paterno);
            $estudiante->materno = mb_strtoupper($request->materno);
            $estudiante->nro_documento = $request->nro_documento;
            $estudiante->foto = $this->save_image($request->foto);
            $estudiante->fecha_nac = $request->fecha_nac;
            $estudiante->anio_egreso = $request->egreso;

            if ($request->pais > 1) {
                $estudiante->ubigeos_nacimiento = 0;
            } else {
                $estudiante->ubigeos_nacimiento = $request->ubigeo;
            }
            $estudiante->edit = "1";
            $estudiante->save();

            DB::commit();
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
        }
        return response()->json($response);
    }
    public function pdfDeclaracionJurada($id)
    {
        $estudiante = DB::table("estudiantes as e")
            ->select(
                "e.*"
            )
            ->where("id", $id)
            ->first();
        $inscripcion = Inscripciones::where("estudiantes_id", $id)->first();
        $periodo = Periodo::find($inscripcion->periodos_id);
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[date('n') - 1];
        $pdf = new PDF();
        PDF::setFooterCallback(function ($pdf) use ($periodo, $inscripcion) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 10, 'Oficina de Computo e Informática || CEPREUNA ' . $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo . ' - Fecha de descarga: ' . date('d/m/Y h:i a'), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        $pdf::SetTitle('Declaración Jurada');
        $pdf::AddPage();
        $pdf::SetMargins(20, 40, 20, true);
        $pdf::setCellHeightRatio(1.5);



        $pdf::Image('images/UNAPUNO.png', 20, 8, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf::Image('images/logo.png', 157, 8, 34, 18, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);

        // $pdf::Image(Storage::disk('fotos')->path($estudiante->foto), 156, 49, 44, 52, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);

        // $pdf::SetFont('helvetica', 'b', 12);

        $pdf::SetFont('helvetica', 'b', 13);
        $pdf::Cell(0, 5, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', 'b', 12);
        $pdf::Cell(0, 5, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(0, 5, 'Jr Acora #235 - Telefono 051-363684', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(0, 5, '“Año del Fortalecimiento de la Soberanía Nacional”', 0, 1, 'C', 0, '', 0);
        $pdf::writeHTML("<hr>");



        $pdf::SetFont('helvetica', 'b', 20);

        $pdf::Cell(0, 5, 'DECLARACIÓN JURADA', 0, 1, 'C', 0, '', 0);

        $pdf::ln();
        $pdf::SetFont('helvetica', '', 12);
        $html = '<p style="text-align:justify">Yo, ' . $estudiante->paterno . ' ' . $estudiante->materno . ' ' . $estudiante->nombres .
            ' Identificado con DNI N° ' . $estudiante->nro_documento . ', estudiante del CEPREUNA CICLO ' .
            $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo . '.' .
            '<p>Declaro bajo juramento que los datos registrados en el sistema de CEPREUNA son correctos y me someto a las sanciones
            establecidas en caso de incumplimiento.</p><p></p><p style="text-align:right">Puno, ' . date('d') . ' de ' . $mes  . ' del ' . date('Y') . '.</p>';

        $pdf::writeHTML($html, true, false, true, false, '');

        $pdf::SetAutoPageBreak(TRUE, 0);

        $pdf::Output('DeclaracionJurada.pdf', 'I');
    }

    public function save_image($base64_image)
    {

        $img = $this->getB64Image($base64_image);

        $ldate = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $img_name = $first . $ldate . '.png';

        Storage::disk('fotos')->put($img_name, $img);
        // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
        return $img_name;
    }
    public function getB64Image($base64_image)
    {

        $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
        $image = base64_decode($image_service_str);

        return $image;
    }
}
