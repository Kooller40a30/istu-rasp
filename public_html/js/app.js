$(function () {
    $('input[type="submit"]').on('click', (event) => {
        var listData = [];
        var dropdowns = $(event.target).parents('form').find('select');
        for (var select of dropdowns) {
            listData.push(select.options[select.selectedIndex].text);
        }
        
        console.log(listData);
        $('#result-content').show();
        $('#title-schedule').html(listData.join("<br>"));
        event.preventDefault();
    });

    $('#group_faculty').on('change', (event) => {
        $.ajax({
            url: '/courses',
            data: { faculty: $(event.target).val() },
            success: (courses) => {
                $('#course-dropdown').html(courses);
                $('#course-dropdown').val("");
                $('#course-dropdown').change();
            }
        });
    });

    $('#course-dropdown').on('change', (event) => {
        var faculty_id = $('#group_faculty').val();
        var course_id = $(event.target).val();
        $.ajax({
            url: '/groups',
            data: { faculty: faculty_id, course: course_id },
            success: (groups) => {                    
                $('#group-dropdown').html(groups);
                $('#group-dropdown').val("");
                $('#group-dropdown').change();
            }
        });            
    });

    $('#teacher_faculty').on('change', (event) => {
        var faculty_id = $(event.target).val();
        $.ajax({
            url: '/departments',
            data: { faculty: faculty_id },
            success: (deps) => {
                $('#department').html(deps);
                $('#department').val("");
                $('#department').change();
            }
        });
    });

    $('#department').on('change', (event) => {
        var faculty_id = $('#teacher_faculty').val();
        var dep_id = $(event.target).val();
        $.ajax({
            url: '/teachers',
            data: { faculty: faculty_id, dep: dep_id },
            success: (html) => {
                $('#teacher').html(html);
                $('#teacher').val("");
                $('#teacher').change();
            }
        })
    });

    $('#room_faculty').on('change', (event) => {
        var faculty_id = $(event.target).val();
        $.ajax({
            url: '/departments',
            data: { faculty: faculty_id },
            success: (deps) => {
                $('#room_department').html(deps);
                $('#room_department').val("");
                $('#room_department').change();
            }
        });
    });

    $('#room_department').on('change', (event) => {
        var faculty_id = $('#room_faculty').val();
        var dep_id = $(event.target).val();
        $.ajax({
            url: '/classrooms',
            data: {
                faculty: faculty_id,
                dep: dep_id
            },
            success: (html) => {
                $('#classroom').html(html);
                $('#classroom').val("");
                $('#classroom').change();
            }
        })
    });
});