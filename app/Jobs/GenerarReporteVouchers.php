<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TCPDF;
use App\Models\Pago;

class GenerarReporteVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inicio, $fin, $tipo, $userId;

    public function __construct($inicio, $fin, $tipo, $userId)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->tipo = $tipo;
        $this->userId = $userId;
    }

    public function handle()
    {
        $pagos = Pago::whereBetween('fecha', [$this->inicio, $this->fin])
            ->when($this->tipo === 'imagenes', function ($query) {
                $query->where(function ($q) {
                    $q->where('voucher', 'like', '%.jpg')
                        ->orWhere('voucher', 'like', '%.jpeg')
                        ->orWhere('voucher', 'like', '%.png');
                });
            })
            ->when($this->tipo === 'documentos', function ($query) {
                $query->where('voucher', 'like', '%.pdf');
            })
            ->get();

        if ($pagos->isEmpty()) return;

        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) mkdir($tempPath, 0775, true);

        $chunks = $pagos->chunk(96);
        $pdfFiles = [];
        $prefijo = $this->tipo === 'documentos' ? 'reporte_vouchersPDF' : 'reporte_vouchersIMGS';

        foreach ($chunks as $i => $lote) {
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->AddPage();

            $col = $row = 0;
            $colsPerPage = 2;
            $rowsPerPage = $this->tipo === 'documentos' ? 3 : 4;
            $voucherWidth = (210 - 20) / $colsPerPage;
            $voucherHeight = (297 - 20) / $rowsPerPage;

            foreach ($lote as $pago) {
                $extension = strtolower(pathinfo($pago->voucher, PATHINFO_EXTENSION));
                $basePath = storage_path("app/public/vouchers/{$pago->voucher}");
                $x = 10 + ($col * $voucherWidth);
                $y = 10 + ($row * $voucherHeight);

                if (file_exists($basePath)) {
                    $pdf->Image($basePath, $x, $y, $voucherWidth, $voucherHeight, 'JPG');
                }

                $pdf->SetDrawColor(0, 0, 0);
                $pdf->Rect($x, $y, $voucherWidth, $voucherHeight, 'D');
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Rect($x + 2, $y + 2, $voucherWidth - 4, 6, 'F');
                $pdf->SetXY($x + 3, $y + 3);
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->Cell(0, 4, "Secuencia: {$pago->secuencia} - DNI: {$pago->nro_documento} - Fecha: {$pago->fecha} - Monto: S/ {$pago->monto}", 0, 1, 'L', false);

                $col++;
                if ($col >= $colsPerPage) { $col = 0; $row++; }
                if ($row >= $rowsPerPage) { $pdf->AddPage(); $row = 0; $col = 0; }
            }

            $path = "$tempPath/{$prefijo}_lote_{$i}.pdf";
            $pdf->Output($path, 'F');
            $pdfFiles[] = $path;
        }

        $zipPath = "$tempPath/{$prefijo}_{$this->inicio}_al_{$this->fin}.zip";
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            foreach ($pdfFiles as $file) $zip->addFile($file, basename($file));
            $zip->close();
        }

        foreach ($pdfFiles as $file) unlink($file);
    }
}
