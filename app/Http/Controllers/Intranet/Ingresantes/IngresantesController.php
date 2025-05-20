<?php

namespace App\Http\Controllers\Intranet\Ingresantes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

use App\Models\Ingresante;

use PhpOffice\PhpSpreadsheet\IOFactory;

class IngresantesController extends Controller
{
    public function importar(Request $request)
    {
        //return "holi";
        // Validar el archivo de Excel cargado
        $request->validate([
            'archivo_excel' => 'required|mimes:xls,xlsx',
        ]);

        // Obtener el archivo cargado
        $archivoExcel = $request->file('archivo_excel');

        // Cargar el archivo de Excel con PhpSpreadsheet
        $spreadsheet = IOFactory::load($archivoExcel);

        // Obtener la hoja activa del archivo (suponiendo que solo hay una hoja)
        $hoja = $spreadsheet->getActiveSheet();

        // Obtener los datos de las filas de la hoja (omitir la primera fila de encabezados)
        $filas = $hoja->toArray(null, true, true, true);
        unset($filas[1]); // Omitir la primera fila (encabezados)

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Iterar sobre las filas para guardar los datos en la tabla "ingresantes"
            foreach ($filas as $fila) {
                Ingresante::create([
                    'dni' => $fila['A'],
                    'nombres' => $fila['B'],
                    'puntaje' => $fila['C'],
                    'carrera' => $fila['D'],
                    'fecha_examen' => $fila['E'], // Cambiar 'E' por la letra de la columna del campo fecha_examen en el archivo de Excel
                ]);
            }

            // Confirmar la transacción
            DB::commit();

            // Redireccionar a la vista de importación con un mensaje de éxito
            return redirect()->back()->with('success', 'Datos importados correctamente.');
        } catch (\Exception $e) {
            // Si ocurre algún error, revertir la transacción
            DB::rollback();

            // Redireccionar a la vista de importación con un mensaje de error
            // return redirect()->back()->with('error', 'Error al importar los datos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al importar los datos: ' . $e->getMessage());
        }
    }
    public function importarIngresantes()
    {
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }
        $response['permisos'] = json_encode($permissions);
        return view("intranet.administracion.importarIngresantes", $response);
    }   

}