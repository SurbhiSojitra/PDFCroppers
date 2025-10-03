<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDfCroppers</title>

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
                <label class="form-check-label" for="sortBySoldBy">Sort by Sold By</label>
                <input class="form-check-input" type="checkbox" name="sortBySoldBy" id="sortBySoldBy">
            </div>

             <div class="form-check">
                 <label class="form-check-label" for="sortCourierWise">Sort Courier Wise</label>
                <input class="form-check-input" type="checkbox" name="sortCourierWise" id="sortCourierWise">
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</body>


</html>

<!-- 
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="noCrop">
                <label class="form-check-label" for="noCrop">Keep Invoice No Crop</label>
            </div>


            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sortCourierWise">
                <label class="form-check-label" for="sortCourierWise">Sort Courier Wise</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="removeWhitespace">
                <label class="form-check-label" for="removeWhitespace">Keep Invoice (remove white space: fit for 4×4 label)</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="treatValmoExpress">
                <label class="form-check-label" for="treatValmoExpress">Treat ValmoExpress same as Valmo</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="printDateTime">
                <label class="form-check-label" for="printDateTime">Print Date Time on Label</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="multiOrderBottom">
                <label class="form-check-label" for="multiOrderBottom">Multi Order at Bottom</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="addPicklist">
                <label class="form-check-label" for="addPicklist">Add Picklist Page After Orders</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="separateReviewOrders">
                <label class="form-check-label" for="separateReviewOrders">Separate Review Orders Using List</label>
            </div> -->