@extends('layout.app')

@section('title', 'Home')

@push('styles')
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
<!-- Optional: Rawline font -->
<link href="https://fonts.googleapis.com/css2?family=Rawline&display=swap" rel="stylesheet">
@endpush
@section('content')

<section>
    <div class="container mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="heading mt-5">
                    <h1>Best E-Commerce Label Crop<br> and PDF Tool Provider</h1>
                </div>
                <div class="para fw-bold text-muted mt-4">
                    <p class="para text-muted fs-6">Get access to premium pdf tools</p>
                </div>
                <a href="{{ route('google.login') }}" class="login-button mt-4">Explore All PDF Tools</a>
            </div>

            <!-- Orbit Section -->
            <div class="orbit-container">
                <div class="orbit-system">
                    <div class="orbit-ring ring-outer"></div>
                    <div class="icon-orbit">
                        <div class="orbit-icon icon-1">
                            <a href="/flipkart/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/flipkart.svg" alt="Flipkart">
                            </a>
                        </div>
                        <div class="orbit-icon icon-2">
                            <a href="/snapdeal/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/snapdeal.svg" alt="Snapdeal">
                            </a>
                        </div>
                        <div class="orbit-icon icon-3">
                            <a href="/myntra/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/myntra.svg" alt="Myntra">
                            </a>
                        </div>
                        <div class="orbit-icon icon-4">
                            <a href="/jiomart/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/jiomart.svg" alt="Jiomart">
                            </a>
                        </div>
                        <div class="orbit-icon icon-5">
                            <a href="/amazon/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/amazon.svg" alt="Amazon">
                            </a>
                        </div>
                        <div class="orbit-icon icon-6">
                            <a href="/shop101/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/shop101.svg" alt="Shop101">
                            </a>
                        </div>
                    </div>

                    <!-- Inner Orbit Icons -->
                    <div class="inner-orbit">
                        <div class="inner-icon inner-icon-1">
                            <a href="/fbf-box-label-crop/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/crop.svg" alt="FBF Box">
                            </a>
                        </div>
                        <div class="inner-icon inner-icon-2">
                            <a href="/sticker-label/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/sticker.svg" alt="Sticker">
                            </a>
                        </div>
                        <div class="inner-icon inner-icon-3">
                            <a href="/crop-other-ecommerce-label/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/other.svg" alt="Other">
                            </a>
                        </div>
                        <div class="inner-icon inner-icon-4">
                            <a href="/protect-pdf/">
                                <img src="https://pdfcroppers.blr1.digitaloceanspaces.com/static/landing/images/protect.svg" alt="Protect">
                            </a>
                        </div>
                    </div>

                    <!-- Central Donut -->
                    <div class="central-donut">
                        <div class="central-core"></div>
                    </div>

                    <!-- Light Effect -->
                    <div class="light-effect"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="interest-section">
    <h2 class="section-title">What is so Interesting?</h2>

    <div class="interest-container">

        <div class="interest-left">
            <h3>Start saving your money using pdfcroppers</h3>
            <p>
                Our tools are designed to save you time and money.
                Calculate your potential savings with our simple calculator.
            </p>
        </div>

        <div class="interest-card">
            <div class="icon">
                📊
            </div>
            <label>Enter Your Avg Daily Order Count :</label>
            <input type="text" placeholder="00">

            <hr>

            <label>Your Yearly Savings :</label>

            <div class="savings-row">
                <span class="currency">₹</span>
                <input type="text">

                <span class="clock">⏱</span>
                <input type="text">

                <span class="hrs">hrs</span>
            </div>
        </div>
    </div>
</section>

<section class="tools-section">
    <div class="container">
        <h2 class="tools-title">Essential Label Crop tools at your fingertips</h2>
        <p class="tools-subtitle">How it Works?</p>
        <div class="tools-steps">

            <div class="step">
                <div class="circle1"><i class="bi bi-download"></i></div>
                <h3>Download Your Labels</h3>
                <p>Download Your Amazon/ <br> Flipkart / Other PDF Labels.</p>
            </div>

            <div class="step">
                <div class="circle1"><i class="bi bi-file-text"></i></div>
                <h3>Select & Customize</h3>
                <p>Select options as per your need and <br> drag and drop your labels.</p>
            </div>

            <div class="step">
                <div class="circle1"><i class="bi bi-file-earmark-arrow-down"></i></div>
                <h3>Download & Print</h3>
                <p>Wait for the magic to complete <br> and download your file. It's that simple.</p>
            </div>
        </div>
    </div>
</section>
@endsection