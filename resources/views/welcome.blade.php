<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Dancing with Death</title>

        <link rel="stylesheet" href="css/app.css">
        <link rel="stylesheet" href="css/sweetalert.css">
    </head>
    <body>
        <div id="app">
            <div class="container">
                <h1>Dancing with Death</h1>
                <appointment></appointment>
            </div>
        </div>
        <script src="js/app.js"></script>
    </body>
</html>
