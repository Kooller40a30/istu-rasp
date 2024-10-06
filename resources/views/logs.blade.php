<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/selectize/dist/css/selectize.bootstrap5.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
</head>

<body>

    <p class="h1">{{$title}}</p>

    <table class="table table-light table-striped table-hover">
        <thead>
            <tr>
                @foreach($errAttrs as $attr)
                <th class="col">{{$attr}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($errors as $error)
            <tr>
                @foreach($error->getAttributes() as $name => $val)
                <td class="col">{{$val}}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>