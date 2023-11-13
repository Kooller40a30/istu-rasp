<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
</head>
<body>
@include('include.header')
<main>
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script>
    $(function () {
        $('input[type="submit"]').on('click', (event) => {
            $('#result-content').show();
            event.preventDefault();
        });

        $('#group_faculty').on('change', (event) => {
            $.ajax({
                url: '/courses/faculty=' + $(event.target).val(),
                success: (courses) => {
                    $('#course-dropdown').html(courses);
                    $('#course-dropdown').val("");
                }
            });
        });

        $('#course-dropdown').on('change', (event) => {
            var faculty_id = $('#group_faculty').val();
            var course = $(event.target).val();
            $.ajax({
                url: '/groups/faculty=' + faculty_id + '/course=' + course,
                success: (groups) => {                    
                    $('#group-dropdown').html(groups);
                    $('#group-dropdown').val("");
                }
            });            
        });
    });
</script>
</body>
</html>
