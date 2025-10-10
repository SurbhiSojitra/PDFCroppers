@extends('layout.app')

@section('title', 'Home')

@push('styles')
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
<!-- Optional: Rawline font -->
<link href="https://fonts.googleapis.com/css2?family=Rawline&display=swap" rel="stylesheet">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 text-center p-0 mt-4">
            <h1>Crop E-commerce Label</h1>
            <p class="mt-4">Crop and sort your meesho PDF Labels in the order you want with the easiest label crop tool available.</p>

            <div class="box mt-5">
                <div class="drop-file">Drag & Drop Your PDF Here</div>
            </div>
        </div>


        <div class="col-md-4 px-5 mb-4 mt-3">
            <form action="{{ route('pdf.process') }}" method="post" enctype="multipart/form-data" target="_blank" style="box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); width:400px; height:auto; padding:20px; border-radius: 20px;">
                @csrf
                <h3>PDF Croppers Tools</h3>
                <div class="mb-3 d-none">
                    <label for="formFileMultiple" class="form-label">Choose Your file</label>
                    <input class="form-control" type="file" name="files[]" id="formFileMultiple" multiple>
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="merge">Merge PDF</label>
                    <input class="form-check-input" type="checkbox" name="merge" id="merge">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="keepInvoice">Keep Invoice</label>
                    <input class="form-check-input" type="checkbox" name="keepInvoice" id="keepInvoice">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="noCrop">Keep Invoice No Crop</label>
                    <input class="form-check-input" type="checkbox" name="noCrop" id="noCrop">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="removeWhitespace">Keep Invoice (remove white space: fit for 4×4 label)</label>
                    <input class="form-check-input" type="checkbox" name="removeWhitespace" id="removeWhitespace">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="sortBySoldBy">Sort by Sold By</label>
                    <input class="form-check-input" type="checkbox" name="sortBySoldBy" id="sortBySoldBy">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="sortCourierWise">Sort Courier Wise</label>
                    <input class="form-check-input" type="checkbox" name="sortCourierWise" id="sortCourierWise">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="printDateTime">Print Date Time on Label</label>
                    <input class="form-check-input" type="checkbox" name="printDateTime" id="printDateTime">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="treatValmoExpress">Treat ValmoExpress same as Valmo</label>
                    <input class="form-check-input" type="checkbox" name="treatValmoExpress" id="treatValmoExpress">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="multiorderBottom">Multi order at Bottom</label>
                    <input class="form-check-input" type="checkbox" name="multiorderBottom" id="multiorderBottom">
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="separateReviewOrders">Separate Review Orders Using List</label>
                    <input class="form-check-input" type="checkbox" name="separateReviewOrders" id="separateReviewOrders">

                    <div id="reviewTextareaContainer" style="display: none;">
                        <textarea name="reviewPdfIds" id="reviewPdfIds" class="form-control" rows="4" placeholder="e.g., 195958732999391616_1,"></textarea>
                    </div>
                </div>

                <div class="form-check mt-3">
                    <label class="form-check-label" for="addPicklist">Add Picklist Page After Orders</label>
                    <input class="form-check-input" type="checkbox" name="addPicklist" id="addPicklist">
                </div>

                <div class="mt-3">
                    <button type="submit" class="mb-3 mt-3" style="color: #fff; background-color: #024263; padding: 6px 50px; border-radius: 20px">Proceed</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const checkbox = document.getElementById('separateReviewOrders');
    const textareaContainer = document.getElementById('reviewTextareaContainer');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            textareaContainer.style.display = 'block';
        } else {
            textareaContainer.style.display = 'none';
        }
    });
</script>

<script>
    const box = document.querySelector('.box');
    const fileInput = document.querySelector('#formFileMultiple');

    // Highlight box on dragover
    box.addEventListener('dragover', (e) => {
        e.preventDefault();
        box.style.borderColor = '#024263';
        box.style.backgroundColor = '#f0f8ff';
    });

    // Remove highlight on dragleave
    box.addEventListener('dragleave', (e) => {
        e.preventDefault();
        box.style.borderColor = 'black';
        box.style.backgroundColor = 'transparent';
    });

    // Handle drop
    box.addEventListener('drop', (e) => {
        e.preventDefault();
        box.style.borderColor = 'black';
        box.style.backgroundColor = 'transparent';

        const files = e.dataTransfer.files;

        // Assign dropped files to the input
        fileInput.files = files;
        
        // Optional: show file names in the box
        const fileNames = Array.from(files).map(f => f.name).join(', ');
        box.querySelector('.drop-file').textContent = fileNames;
    });

    // Optional: click box to open file picker
    box.addEventListener('click', () => {
        fileInput.click();
    });

    // Sync file input selection with box display
    fileInput.addEventListener('change', () => {
        const files = fileInput.files;
        const fileNames = Array.from(files).map(f => f.name).join(', ');
        box.querySelector('.drop-file').textContent = fileNames || 'Drag & Drop Your PDF Here';
    });
</script>

@endsection