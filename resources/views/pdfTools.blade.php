<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDfTools</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>

<style>
    .container {
        max-width: 700px;
    }
</style>

<body>
    <div class="container mt-5">
        <form action="{{ route('pdf.process') }}" method="post" enctype="multipart/form-data" target="_blank">
            @csrf
            <h1>PDF Croppers</h1>

            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Choose Your file</label>
                <input class="form-control" type="file" name="files[]" id="formFileMultiple" multiple>
            </div>

            <div class="form-check">
                <label class="form-check-label" for="merge">Merge PDF</label>
                <input class="form-check-input" type="checkbox" name="merge" id="merge">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="keepInvoice">Keep Invoice</label>
                <input class="form-check-input" type="checkbox" name="keepInvoice" id="keepInvoice">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="noCrop">Keep Invoice No Crop</label>
                <input class="form-check-input" type="checkbox" name="noCrop" id="noCrop">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="removeWhitespace">Keep Invoice (remove white space: fit for 4×4 label)</label>
                <input class="form-check-input" type="checkbox" name="removeWhitespace" id="removeWhitespace">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="sortBySoldBy">Sort by Sold By</label>
                <input class="form-check-input" type="checkbox" name="sortBySoldBy" id="sortBySoldBy">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="sortCourierWise">Sort Courier Wise</label>
                <input class="form-check-input" type="checkbox" name="sortCourierWise" id="sortCourierWise">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="printDateTime">Print Date Time on Label</label>
                <input class="form-check-input" type="checkbox" name="printDateTime" id="printDateTime">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="treatValmoExpress">Treat ValmoExpress same as Valmo</label>
                <input class="form-check-input" type="checkbox" name="treatValmoExpress" id="treatValmoExpress">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="multiorderBottom">Multi order at Bottom</label>
                <input class="form-check-input" type="checkbox" name="multiorderBottom" id="multiorderBottom">
            </div>

            <div class="form-check">
                <label class="form-check-label" for="separateReviewOrders">Separate Review Orders Using List</label>
                <input class="form-check-input" type="checkbox" name="separateReviewOrders" id="separateReviewOrders">

                <div id="reviewTextareaContainer" style="display: none;">
                    <textarea name="reviewPdfIds" id="reviewPdfIds" class="form-control" rows="4" placeholder="e.g., 195958732999391616_1,"></textarea>
                </div>
            </div>

            <div class="form-check">
                <label class="form-check-label" for="addPicklist">Add Picklist Page After Orders</label>
                <input class="form-check-input" type="checkbox" name="addPicklist" id="addPicklist">
            </div>


            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

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
    </div>
</body>


</html>
