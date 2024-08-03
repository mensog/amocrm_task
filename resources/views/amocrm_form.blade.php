<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AmoCRM Task</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



</head>

<body>
    <div class="container">
        <form action="{{ route('amocrm.submit') }}" method="POST" class="w-50 p-5">
            @csrf
            <div class="row mb-3">
                <label for="input-name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control" id="input-name">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" id="email" class="form-control" id="input-email">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-phone" class="col-sm-2 col-form-label">Phone number</label>
                <div class="col-sm-10">
                    <input type="tel" name="phone" id="phone" class="form-control" id="input-phone">
                </div>
            </div>
            <div class="row mb-3">
                <label for="input-price" class="col-sm-2 col-form-label">Price</label>
                <div class="col-sm-10">
                    <input type="number" name="price" id="price" class="form-control" id="input-price">
                </div>
            </div>

            <input type="hidden" id="time_spent" name="time_spent" value="0">

            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>


    <script>
        let startTime = Date.now();
        
        window.onload = () => {
            let form = document.querySelector('form');
            form.onsubmit = () => {
                let timeSpent = Math.floor((Date.now() - startTime) / 1000);
                document.getElementById('time_spent').value = timeSpent > 30 ? 1 : 0;
            };
        };
    </script>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

</html>
