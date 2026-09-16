<?php

namespace App\Http\Controllers;

use App\Jobs\ExportObatExcel;
use App\Jobs\ExportObatPdf;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObatController extends Controller
{
    public function index()
    {
        return view('obat.index');
    }

    public function data(Request $request)
    {
        $obat = Obat::filter($request->only(['search', 'category', 'stock_status']))
            ->orderBy('name')
            ->paginate(10);

        // Ambil daftar kategori unik untuk dropdown filter
        $categories = Obat::distinct()->orderBy('category')->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $obat->items(),
            'pagination' => [
                'current_page' => $obat->currentPage(),
                'last_page' => $obat->lastPage(),
                'per_page' => $obat->perPage(),
                'total' => $obat->total(),
                'from' => $obat->firstItem(),
                'to' => $obat->lastItem(),
            ],
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:obat,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $obat = Obat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan',
            'data' => $obat,
        ], 201);
    }

    /**
     * Detail satu data obat (read-only, via AJAX).
     */
    public function show(Obat $obat)
    {
        return response()->json([
            'success' => true,
            'data' => $obat,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Obat $obat)
    {
        return response()->json([
            'success' => true,
            'data' => $obat,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:obat,code,'.$obat->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $obat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil diperbarui',
            'data' => $obat,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Obat $obat)
    {
        $obat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil dihapus',
        ]);
    }

    // ===== EXPORT EXCEL dengan progress nyata =====

    private function exportQuery(array $filters)
    {
        return Obat::filter($filters);
    }

    // 1. Mulai export: hitung total, simpan status 0%, dispatch Job
    public function startExport(Request $request)
    {
        $filters = $request->only(['search', 'category', 'stock_status']);
        $total = $this->exportQuery($filters)->count();
        $now = now();

        $exportId = (string) Str::uuid();
        $key = 'obat_export:'.$exportId;

        $initial = [
            'status' => 'processing',
            'processed' => 0,
            'total' => $total,
            'percent' => 0,
            'file' => null,
            'error' => null,
            // Jam-tanggal dipakai untuk judul isi file + nama file download
            'generated_at' => $now->translatedFormat('d F Y H:i:s'),
            'generated_at_file' => $now->format('Ymd-His'),
        ];
        Cache::put($key, $initial, $now->copy()->addHour());

        if ($total === 0) {
            Cache::put($key, [...$initial, 'status' => 'done', 'percent' => 100], $now->copy()->addHour());
        } else {
            ExportObatExcel::dispatch($exportId, $filters);
        }

        return response()->json([
            'success' => true,
            'export_id' => $exportId,
            'total' => $total,
        ]);
    }

    // 2. Cek status: dipolling frontend tiap 1 detik
    public function exportStatus(string $id)
    {
        $state = Cache::get('obat_export:'.$id);

        if (! $state) {
            return response()->json(['success' => false, 'message' => 'Export tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, ...$state]);
    }

    // 3. Download file kalau sudah done
    public function downloadExport(string $id)
    {
        $state = Cache::get('obat_export:'.$id);

        if (! $state || $state['status'] !== 'done' || empty($state['file'])) {
            abort(404, 'File export belum siap.');
        }

        if (! Storage::disk('local')->exists($state['file'])) {
            abort(404, 'File export tidak ditemukan.');
        }

        $stamp = $state['generated_at_file'] ?? now()->format('Ymd-His');

        return Storage::disk('local')->download($state['file'], "data-obat-{$stamp}.xlsx");
    }

    // ===== EXPORT PDF: sama seperti Excel (Job + progress), beda isi laporan =====
    // DomPDF tidak kuat render puluhan ribu baris sekaligus (Cellmap makan RAM),
    // jadi PDF dibatasi. Untuk data besar pakai Excel.
    public const MAX_PDF_ROWS = 2000;

    public function startPdfExport(Request $request)
    {
        $filters = $request->only(['search', 'category', 'stock_status']);
        $total = $this->exportQuery($filters)->count();
        $now = now();

        if ($total > self::MAX_PDF_ROWS) {
            return response()->json([
                'success' => false,
                'message' => 'Data terlalu banyak ('.number_format($total, 0, ',', '.').' baris). Maksimal '.number_format(self::MAX_PDF_ROWS, 0, ',', '.').' baris untuk PDF. Persempit filter atau gunakan Export Excel.',
                'total' => $total,
            ], 422);
        }

        $exportId = (string) Str::uuid();
        $key = 'obat_export_pdf:'.$exportId;

        $initial = [
            'status' => 'processing',
            'processed' => 0,
            'total' => $total,
            'percent' => 0,
            'file' => null,
            'error' => null,
            'generated_at' => $now->translatedFormat('d F Y H:i:s'),
            'generated_at_file' => $now->format('Ymd-His'),
        ];
        Cache::put($key, $initial, $now->copy()->addHour());

        if ($total === 0) {
            Cache::put($key, [...$initial, 'status' => 'done', 'percent' => 100], $now->copy()->addHour());
        } else {
            ExportObatPdf::dispatch($exportId, $filters);
        }

        return response()->json([
            'success' => true,
            'export_id' => $exportId,
            'total' => $total,
        ]);
    }

    public function pdfStatus(string $id)
    {
        $state = Cache::get('obat_export_pdf:'.$id);

        if (! $state) {
            return response()->json(['success' => false, 'message' => 'Export tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, ...$state]);
    }

    public function downloadPdf(string $id)
    {
        $state = Cache::get('obat_export_pdf:'.$id);

        if (! $state || $state['status'] !== 'done' || empty($state['file'])) {
            abort(404, 'File export belum siap.');
        }

        if (! Storage::disk('local')->exists($state['file'])) {
            abort(404, 'File export tidak ditemukan.');
        }

        $stamp = $state['generated_at_file'] ?? now()->format('Ymd-His');

        return Storage::disk('local')->download($state['file'], "laporan-stok-obat-{$stamp}.pdf");
    }
}
