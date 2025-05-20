<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Pago;
use Intervention\Image\Facades\Image;

class GenerarVouchersImagen extends Command
{
    protected $signature = 'vouchers:imagen {carpeta=01-2025}';
    protected $description = 'Genera imágenes con los datos de cada pago';

    public function handle()
    {
        $carpeta = $this->argument('carpeta');
        $rutaOrigen = "public/vouchers/{$carpeta}";
        $rutaDestino = "public/vouchers_con_datos/{$carpeta}";

        if (!Storage::exists($rutaOrigen)) {
            $this->error("No existe la carpeta: $rutaOrigen");
            return;
        }

        Storage::makeDirectory($rutaDestino);
        $archivos = Storage::files($rutaOrigen);

        $this->info("Procesando " . count($archivos) . " archivos...");

        foreach ($archivos as $archivo) {
            $nombre = basename($archivo);
            $pago = Pago::where('voucher', 'like', "{$carpeta}/%{$nombre}")->first();

            $image = Image::canvas(800, 400, '#ffffff');

            // Usar fuentes internas de GD
            $image->text("Archivo: $nombre", 50, 50, function ($font) {
                $font->size(20);
                $font->color('#000000');
            });

            if ($pago) {
                $image->text("Secuencia: $pago->secuencia", 50, 100, function ($font) {
                    $font->size(20);
                    $font->color('#000000');
                });
                $image->text("Monto:     S/ $pago->monto", 50, 150, function ($font) {
                    $font->size(20);
                    $font->color('#000000');
                });
                $image->text("Fecha:     $pago->fecha", 50, 200, function ($font) {
                    $font->size(20);
                    $font->color('#000000');
                });
                $image->text("Documento: $pago->nro_documento", 50, 250, function ($font) {
                    $font->size(20);
                    $font->color('#000000');
                });
            } else {
                $image->text("¡No encontrado en BD!", 50, 120, function ($font) {
                    $font->size(20);
                    $font->color('#ff0000');
                });
            }

            $outputPath = storage_path("app/{$rutaDestino}/{$nombre}.png");
            $image->save($outputPath);
        }

        $this->info("¡Imágenes generadas en storage/app/{$rutaDestino}!");
    }
}