<?php

namespace App\Jobs;

use App\Models\Obat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ExportObatExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $exportId,
        public array $filters = []
    ) {}

    private function cacheKey(): string
    {
        return 'obat_export:'.$this->exportId;
    }

    private function buildQuery()
    {
        return Obat::filter($this->filters)->orderBy('id');
    }

    public function handle(): void
    {
        $key = $this->cacheKey();
        $state = Cache::get($key);

        if (! $state) {
            return; // export dibatalkan / kedaluwarsa
        }

        $total = (int) ($state['total'] ?? 0);
        $generatedAt = $state['generated_at'] ?? now()->translatedFormat('d F Y H:i:s');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Obat');

        // Judul laporan + jam tanggal export
        $sheet->setCellValue('A1', 'DATA OBAT — '.$generatedAt);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Tanggal/Waktu: '.$generatedAt.' | Jumlah: '.number_format($total, 0, ',', '.').' data');

        // Baris 4 = header kolom (baris 3 dikosongkan sebagai jarak)
        $headers = ['Kode', 'Nama', 'Kategori', 'Satuan', 'Harga Beli', 'Harga Jual', 'Stok', 'Min. Stok', 'Kadaluarsa', 'Catatan'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);

        $processed = 0;
        $row = 5; // data mulai dari baris 5

        // Ambil 500 baris sekaligus, hemat memori.
        // Setiap selesai 1 chunk -> update progress di cache.
        $this->buildQuery()->chunk(500, function ($obats) use (&$processed, &$row, $sheet, $key, $total, $generatedAt) {
            foreach ($obats as $obat) {
                $sheet->fromArray([
                    $obat->code,
                    $obat->name,
                    $obat->category,
                    $obat->unit,
                    (float) $obat->purchase_price,
                    (float) $obat->selling_price,
                    (int) $obat->stock,
                    (int) $obat->minimum_stock,
                    $obat->expired_date,
                    $obat->notes,
                ], null, 'A'.$row);
                $row++;
                $processed++;
            }

            // INI KUNCINYA: progress nyata = jumlah baris yang sudah ditulis / total
            $percent = $total > 0 ? (int) round($processed / $total * 100) : 100;
            Cache::put($key, [
                'status' => 'processing',
                'processed' => $processed,
                'total' => $total,
                'percent' => min($percent, 100),
                'file' => null,
                'error' => null,
                'generated_at' => $generatedAt,
            ], now()->addHour());
        });

        // Simpan file ke storage/app/exports/{id}.xlsx
        Storage::disk('local')->makeDirectory('exports');
        $relativePath = 'exports/'.$this->exportId.'.xlsx';
        $absolutePath = Storage::disk('local')->path($relativePath);
        (new Xlsx($spreadsheet))->save($absolutePath);
        $spreadsheet->disconnectWorksheets();

        Cache::put($key, [
            'status' => 'done',
            'processed' => $total,
            'total' => $total,
            'percent' => 100,
            'file' => $relativePath,
            'error' => null,
            'generated_at' => $generatedAt,
        ], now()->addHour());
    }

    public function failed(?Throwable $e = null): void
    {
        $prev = Cache::get($this->cacheKey()) ?? [];
        Cache::put($this->cacheKey(), [
            'status' => 'failed',
            'processed' => $prev['processed'] ?? 0,
            'total' => $prev['total'] ?? 0,
            'percent' => $prev['percent'] ?? 0,
            'file' => null,
            'error' => $e ? $e->getMessage() : 'Job gagal tanpa pesan error.',
            'generated_at' => $prev['generated_at'] ?? null,
        ], now()->addHour());
    }
}
