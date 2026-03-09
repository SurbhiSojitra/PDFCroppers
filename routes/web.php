<?php

use App\Http\Controllers\PdfCroppersContoller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

    Route::get('/', [PdfCroppersContoller::class, 'index'])->name('home');
    Route::get('/pdfTools', [PdfCroppersContoller::class, 'pdfTools'])->name('pdfTools');
    Route::get('/login', [PdfCroppersContoller::class, 'login'])->name('login');
    Route::post('/process-pdf', [PdfCroppersContoller::class, 'PdfProcess'])->name('pdf.process');
    Route::get('/register', [PdfCroppersContoller::class, 'register'])->name('register');
    Route::get('auth/google', [PdfCroppersContoller::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [PdfCroppersContoller::class, 'handleGoogleCallback'])->name('google.login.callback');

    Route::get('auth/google/register', [PdfCroppersContoller::class, 'redirectToGoogleRegister'])->name('google.register');
    Route::get('auth/google/register/callback', [PdfCroppersContoller::class, 'handleGoogleRegisterCallback'])->name('google.register.callback');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
