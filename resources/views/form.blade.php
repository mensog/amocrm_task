<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



</head>

<body>
    <div class="container">
        <form action="{{ route('form-submit') }}" class="w-50 p-5">
            <div class="row mb-3">
                <label for="input-name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="input-name">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="input-email">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-phone" class="col-sm-2 col-form-label">Phone number</label>
                <div class="col-sm-10">
                    <input type="tel" class="form-control" id="input-phone">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-price" class="col-sm-2 col-form-label">Price</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="input-price">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
