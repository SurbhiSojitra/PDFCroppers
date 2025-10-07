<?php

use App\Http\Controllers\PdfCroppersContoller;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [PdfCroppersContoller::class, 'index'])->name('home');
Route::get('/pdfTools', [PdfCroppersContoller::class, 'pdfTools'])->name('pdfTools');

Route::post('/process-pdf', [PdfCroppersContoller::class, 'PdfProcess'])->name('pdf.process');


