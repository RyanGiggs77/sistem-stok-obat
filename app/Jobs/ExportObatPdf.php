<?php

namespace App\Jobs;

use App\Models\Obat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExportObatPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $exportId,
        public array $filters = []
    ) {}

    private function cacheKey(): string
    {
        return 'obat_export_pdf:'.$this->exportId;
    }

    private function buildQuery()
    {
        return Obat::filter($this->filters)->orderBy('name');
    }

    public function handle(): void
    {
        $key = $this->cacheKey();
        $state = Cache::get($key);

        if (! $state) {
            return;
        }

        $total = (int) ($state['total'] ?? 0);
        $generatedAt = $state['generated_at'] ?? now()->translatedFormat('d F Y H:i:s');

        // Pengaman ganda: kalau ada yang dispatch langsung tanpa lewat controller
        if ($total > 2000) {
            throw new \RuntimeException('Data terlalu banyak ('.number_format($total, 0, ',', '.').' baris). Maksimal 2.000 baris untuk PDF. Persempit filter atau gunakan Excel.');
        }

        $processed = 0;
        $allRows = collect();

        // Tahap 1 (progress nyata): ambil data 500-500, tiap chunk update Cache.
        // Jadi bar 0% -> 90% = proses ambil data beneran, bukan animasi.
        $this->buildQuery()->chunk(500, function ($obats) use (&$processed, &$allRows, $key, $total, $generatedAt) {
            foreach ($obats as $obat) {
                $allRows->push($obat);
                $processed++;
            }

            // Sisakan 10% terakhir untuk tahap render PDF, jadi progress ambil data max 90%.
            $percent = $total > 0 ? (int) round($processed / $total * 90) : 90;
            Cache::put($key, [
                'status' => 'processing',
                'processed' => $processed,
                'total' => $total,
                'percent' => min($percent, 90),
                'file' => null,
                'error' => null,
                'generated_at' => $generatedAt,
            ], now()->addHour());
        });

        // Tahap 2: render PDF dari data yang sudah terkumpul.
        $filters = $this->filters;
        $activeFilters = collect([
            ! empty($filters['search']) ? 'Cari: '.$filters['search'] : null,
            ! empty($filters['category']) ? 'Kategori: '.$filters['category'] : null,
            ! empty($filters['stock_status']) ? 'Status: '.$filters['stock_status'] : null,
        ])->filter()->implode(' | ');

        $html = view('obat.export-pdf', [
            'obats' => $allRows,
            'total' => $total,
            'generatedAt' => $generatedAt,
            'activeFilters' => $activeFilters,
        ])->render();

        Storage::disk('local')->makeDirectory('exports');
        $relativePath = 'exports/'.$this->exportId.'.pdf';
        $absolutePath = Storage::disk('local')->path($relativePath);
        Pdf::loadHTML($html)->setPaper('a4', 'landscape')->save($absolutePath);

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
