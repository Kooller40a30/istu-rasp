const FilterManager = {
    init() {
        // Инициализация Selectize для всех элементов с классом .selectize-js
        this.applySelectize();
        // Привязка всех обработчиков событий
        this.bindEvents();
    },

    // Инициализация Selectize для указанного селектора (по умолчанию для всех .selectize-js)
    applySelectize(selector = '.selectize-js') {
        $(selector).each(function () {
            const $el = $(this);
            if ($el[0].selectize) {
                $el[0].selectize.destroy();
            }
            $el.selectize({
                placeholder: 'Выберите значение',
                allowEmptyOption: true,
                onChange: function () {
                    $el.trigger('change');
                }
            });
        });
    },

    // Сброс и обновление опций Selectize с новыми данными, переданными как HTML строки
    resetSelectize($select, newOptionsHtml) {
        // Удаляем старый Selectize, если он есть
        if ($select[0].selectize) {
            $select[0].selectize.destroy(); // Полный демонтаж Selectize
        }
    
        // Обновляем HTML опций
        $select.html(newOptionsHtml);
    
        // Реинициализируем Selectize заново
        $select.selectize({
            placeholder: 'Выберите значение',
            allowEmptyOption: true,
            onChange: function () {
                $select.trigger('change');
            }
        });
    
        // Явно сбрасываем значение
        $select.val("").change();
    },    

    // Универсальная функция для AJAX-запросов и обновления расписания
    fetchAndRender(url, data, button) {
        const textSpan = button.find('.btn-text');
        const spinner = button.find('.spinner-border');

        textSpan.addClass('d-none');
        spinner.removeClass('d-none');
        button.prop('disabled', true);
        this.showLoading();

        $.get(url, data, (html, xhr) => {
            $('#result-schedule').html(html).change();
            this.hideLoading();
            textSpan.removeClass('d-none');
            spinner.addClass('d-none');
            button.prop('disabled', false);
        }).fail(() => {
            this.hideLoading();
            textSpan.removeClass('d-none');
            spinner.addClass('d-none');
            button.prop('disabled', false);
            alert('Ошибка загрузки расписания');
        });
    },

    // Показать индикатор загрузки
    showLoading() {
        $('#loading-indicator').show();
    },

    // Скрыть индикатор загрузки
    hideLoading() {
        $('#loading-indicator').hide();
    },

    // Привязка всех обработчиков событий для фильтров и кнопок
    bindEvents() {
        // Кнопки "Показать"
        $('#btn-group').on('click', (event) => {
            event.preventDefault();
            const data = $(event.target).parents('form').serializeArray();
            this.fetchAndRender('/groups_schedule', data, $(event.target));
        });

        $('#btn-teacher').on('click', (event) => {
            event.preventDefault();
            const data = $(event.target).parents('form').serializeArray();
            this.fetchAndRender('/teacher_schedule', data, $(event.target));
        });

        $('#btn-room').on('click', (event) => {
            event.preventDefault();
            const data = $(event.target).parents('form').serializeArray();
            this.fetchAndRender('/classroom_schedule', data, $(event.target));
        });

        // Фильтры для групп
        $('#group_faculty').on('change', function () {
            const faculty_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/courses',
                data: { faculty: faculty_id },
                success: (courses) => {
                    FilterManager.resetSelectize($('#course-dropdown'), courses);
                }
            });
        });

        $('#course-dropdown').on('change', function () {
            const faculty_id = $('#group_faculty')[0].selectize?.getValue() || $('#group_faculty').val();
            const course_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/groups',
                data: { faculty: faculty_id, course: course_id },
                success: (groups) => {
                    FilterManager.resetSelectize($('#group-dropdown'), groups);
                }
            });
        });

        // Фильтры для преподавателей
        $('#teacher_faculty').on('change', function () {
            const faculty_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/departments',
                data: { faculty: faculty_id },
                success: (deps) => {
                    FilterManager.resetSelectize($('#department'), deps);
                }
            });
        });

        $('#department').on('change', function () {
            const faculty_id = $('#teacher_faculty')[0].selectize?.getValue() || $('#teacher_faculty').val();
            const dep_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/teachers',
                data: { faculty: faculty_id, dep: dep_id },
                success: (html) => {
                    FilterManager.resetSelectize($('#teacher'), html);
                }
            });
        });

        // Фильтры для аудиторий
        $('#room_faculty').on('change', function () {
            const faculty_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/departments',
                data: { faculty: faculty_id, for_room: 1 },
                success: (deps) => {
                    FilterManager.resetSelectize($('#room_department'), deps);
                    // Сбрасываем аудитории
                    FilterManager.resetSelectize($('#classroom'), '<option value="">Выберите аудиторию</option>');
                }
            });
        });

        $('#room_department').on('change', function () {
            const faculty_id = $('#room_faculty')[0].selectize?.getValue() || $('#room_faculty').val();
            const dep_id = this.selectize ? this.selectize.getValue() : $(this).val();
            $.ajax({
                url: '/classrooms',
                data: { faculty: faculty_id, dep: dep_id },
                success: (html) => {
                    FilterManager.resetSelectize($('#classroom'), html);
                }
            });
        });

        // Корректировка смещения для ячеек с rowspan
        $('#result-schedule').on('change', () => {
            const thead = $('#result-schedule').find('thead')[0];
            const theadHeight = thead?.clientHeight || 0;
            $('tbody').find('td[rowspan]').each((_, elem) => {
                elem.style.top = theadHeight + 'px';
            });
        });
    }
};

$(function () {
    FilterManager.init();
});