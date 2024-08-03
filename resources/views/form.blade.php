<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit to AmoCRM</title>
</head>
<body>
    <form action="{{ route('amocrm.submit') }}" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" required>
        
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required>
        
        <input type="hidden" id="time_spent" name="time_spent" value="0">
        
        <button type="submit">Submit</button>
    </form>

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
</html>
