$(function () {

    
    function showLoading() {
        $('#loading-indicator').show();
    }

    function hideLoading() {
        $('#loading-indicator').hide();
    }

    function fetchAndRender(url, data, button) {
        const textSpan = button.find('.btn-text');
        const spinner = button.find('.spinner-border');
    
        // Показать спиннер, спрятать текст
        textSpan.addClass('d-none');
        spinner.removeClass('d-none');
        button.prop('disabled', true);
    
        showLoading();
    
        $.get(url, data, (html, xhr) => {
            $('#result-schedule').html(html).change();
            hideLoading();
    
            // Вернуть кнопку в исходное состояние
            textSpan.removeClass('d-none');
            spinner.addClass('d-none');
            button.prop('disabled', false);
        }).fail(() => {
            hideLoading();
            textSpan.removeClass('d-none');
            spinner.addClass('d-none');
            button.prop('disabled', false);
            alert('Ошибка загрузки расписания');
        });
    }
    
    

    $('#btn-group').on('click', function (event) {
        event.preventDefault();
        const data = $(this).parents('form').serializeArray();
        fetchAndRender('/groups_schedule', data, $(this));
    });
    
    $('#btn-teacher').on('click', function (event) {
        event.preventDefault();
        const data = $(this).parents('form').serializeArray();
        fetchAndRender('/teacher_schedule', data, $(this));
    });
    
    $('#btn-room').on('click', function (event) {
        event.preventDefault();
        const data = $(this).parents('form').serializeArray();
        fetchAndRender('/classroom_schedule', data, $(this));
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

    $('#result-schedule').on('change', (event) => {
        var thead = $('#result-schedule').find('thead')[0]; 
        var theadHeight = thead.clientHeight;
        $('tbody').find('td[rowspan]').each((index, elem) => {
            elem.style.top = theadHeight + 'px';
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
            data: { faculty: faculty_id, for_room: 1 },
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