<?php

use App\Http\Controllers\PdfCroppersContoller;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [PdfCroppersContoller::class, 'index'])->name('home');
Route::post('/process-pdf', [PdfCroppersContoller::class, 'PdfProcess'])->name('pdf.process');