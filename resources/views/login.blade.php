@extends('layout.app')

@section('title', 'Home')

@push('styles')
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
<!-- Optional: Rawline font -->
<link href="https://fonts.googleapis.com/css2?family=Rawline&display=swap" rel="stylesheet">
@endpush

@section('content')

<div class="container login-card" style=" display:flex; justify-content:center; align-items:center">
    <div class="card mt-5 mb-5">
        <img src="https://www.pdfgear.com/img/new-tools/crop-pdf/free-online.png" alt="crop-pdf">

        <a href="{{ route('google.login') }}" class="login-button-google mt-5 mb-4">Login With Google</a>

        <div class="para mb-3">
            <p>Not Registered Yet? Create Your Account</p>
        </div>

        <div class="line"></div>

        <div class="service mt-4 mb-5">By login & using our services, you agree to our <br> <a href="">Terms & Conditions of Service</a> and <a href="">Privacy Policy</a></div>
    </div>
</div>

@endsection