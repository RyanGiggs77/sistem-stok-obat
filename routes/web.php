<?php

use App\Http\Controllers\ObatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(ObatController::class)->group(function () {
    Route::get('/obat/data', 'data')->name('obat.data');
    Route::get('/obat', 'index')->name('obat.index');
    Route::post('/obat', 'store')->name('obat.store');
    Route::post('/obat/export-excel/start', 'startExport')->name('obat.export.start');
    Route::get('/obat/export-excel/status/{id}', 'exportStatus')->name('obat.export.status');
    Route::get('/obat/export-excel/download/{id}', 'downloadExport')->name('obat.export.download');
    Route::post('/obat/export-pdf/start', 'startPdfExport')->name('obat.export.pdf.start');
    Route::get('/obat/export-pdf/status/{id}', 'pdfStatus')->name('obat.export.pdf.status');
    Route::get('/obat/export-pdf/download/{id}', 'downloadPdf')->name('obat.export.pdf.download');
    Route::get('/obat/{obat}/edit', 'edit')->name('obat.edit');
    Route::get('/obat/{obat}', 'show')->name('obat.show');
    Route::put('/obat/{obat}', 'update')->name('obat.update');
    Route::delete('/obat/{obat}', 'destroy')->name('obat.destroy');
});
