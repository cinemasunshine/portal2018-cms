/**
 * oyako_cinema/form.js
 */
$(function(){
    var $form = $('form[name="oyako_cinema"]');

    var $titleField = $form.find('.form-group.title');
    var $selectTitleModal = $('#selectTitleModal');

    var $schedulesWrap = $form.find('#schedules-wrap');

    $selectTitleModal.on('selected.cs.title', function(event, title) {
        setTitle(title);
    });

    /**
     * set title
     *
     * @param {Object} title
     */
    function setTitle(title) {
        $titleField.find('input[name="title_id"]').val(title.id);
        $titleField.find('input[name="title_name"]').val(title.name);
        $titleField.find('.title-name').text(title.name);

        $form.find('input[name="start_date"]').val(title.publishing_expected_date);
    }

    var scheduleIndex = 0;
    var $schedulesWrap = $form.find('#schedules-wrap');
    var scheduleFiledsetTmpl = $.templates('#scheduleFiledsetTmpl');

    var $scheduleModal = $('#oyakoCinemaScheduleModal');

    $scheduleModal.on('new-schedule.cs.oyako', function (e, disabledDates) {
        var title = '開催日追加';
        $(this).find('.modal-title').text(title);

        var $form = $(this).find('.form');

        // initialize form
        $form.find('input[name="index"]').val('');

        $form.find('input[name="date"]').val('').prop('disabled', false);
        $form.find('input[name="date"]').datetimepicker('setOptions', { disabledDates: disabledDates })

        $form.find('input[name="theaters[]"]').prop('checked', false);

        $(this).find('.modal-footer .btn-success').hide();
        $(this).find('.modal-footer .btn-create').show();

        $(this).modal('show');
    });

    $scheduleModal.on('edit-schedule.cs.oyako', function (e, index, date, theaters) {
        var title = '開催日「' + date + '」編集';

        $(this).find('.modal-title').text(title);

        var $form = $(this).find('.form');

        // initialize form
        $form.find('input[name="index"]').val(index);
        $form.find('input[name="date"]').val(date).prop('disabled', true);
        $form.find('input[name="theaters[]"]').prop('checked', false);

        $.each(theaters, function (i, theater) {
            $form.find('input[name="theaters[]"][value="' + theater + '"]').prop('checked', true);
        });

        $(this).find('.modal-footer .btn-success').hide();
        $(this).find('.modal-footer .btn-update').show();

        $(this).modal('show');
    });

    $scheduleModal.find('.datepicker').datetimepicker(datepickerOption);

    $scheduleModal.find('.btn-check-all').click(function(e) {
        $(this).closest('.form-group').find(':checkbox').prop('checked', true);
    });

    $scheduleModal.find('.btn-uncheck-all').click(function() {
        $(this).closest('.form-group').find(':checkbox').prop('checked', false);
    });

    $scheduleModal.find('.btn-create').click(function() {
        var $form = $scheduleModal.find('.form');
        var date = $form.find('input[name="date"]').val();

        if (date === '') {
            alert('日付を入力してください。');
            return;
        }

        var theaters = [];

        $form.find('input[name="theaters[]"]:checked').each(function(i, input) {
            let theater = {};
            theater.id = $(this).val();
            theater.name = $(this).next('label').text();
            theaters.push(theater);
        });

        if (theaters.length === 0) {
            alert('劇場を選択してください。');
            return;
        }

        $schedulesWrap.trigger('create-schedule.cs.oyako', [date, theaters]);

        $scheduleModal.modal('hide');
    });

    $scheduleModal.find('.btn-update').click(function() {
        var $form = $scheduleModal.find('.form');
        var index = $form.find('input[name="index"]').val();
        var date = $form.find('input[name="date"]').val();
        var theaters = [];

        $form.find('input[name="theaters[]"]:checked').each(function(i, input) {
            let theater = {};
            theater.id = $(this).val();
            theater.name = $(this).next('label').text();
            theaters.push(theater);
        });

        if (theaters.length === 0) {
            alert('劇場を選択してください。');
            return;
        }

        $schedulesWrap.trigger('update-schedule.cs.oyako', [index, date, theaters]);

        $scheduleModal.modal('hide');
    });


    $schedulesWrap.on('create-schedule.cs.oyako', function (e, date, theaters) {
        var $fieldset = $(scheduleFiledsetTmpl.render({
            index: scheduleIndex,
            date: date,
            theaters: theaters
        }));
        scheduleIndex++;

        $(this).append($fieldset);
    });

    $schedulesWrap.on('update-schedule.cs.oyako', function (e, index, date, theaters) {
        var $oldFieldset = $(this).find('.schedule[data-index="' + index + '"]');

        var $newFieldset = $(scheduleFiledsetTmpl.render({
            index: index,
            date: date,
            theaters: theaters
        }));

        $oldFieldset.after($newFieldset);
        $oldFieldset.remove();
    });

    $schedulesWrap.on('click', '.schedule .btn-edit', function () {
        var $schedule = $(this).closest('.schedule');
        var index = $schedule.data('index');
        var date = $schedule.find('input[name*="[date]"]').val();
        var theaters = [];
        $schedule.find('input[name*="[theaters]"]').each(function () {
            theaters.push($(this).val());
        });

        $scheduleModal.trigger('edit-schedule.cs.oyako', [index, date, theaters]);
    });

    $schedulesWrap.on('click', '.schedule .btn-delete', function () {
        var $schedule = $(this).closest('.schedule');
        var date = $schedule.find('input[name*="[date]"]').val();
        var message = '開催日「' + date + '」を削除してよろしいですか？';

        if (confirm(message)) {
            $(this).closest('.schedule').remove();
        }
    });


    $form.find('.field-schedule .btn-add').click(function () {
        var disabledDates = [];

        $schedulesWrap.find('.schedule input[name*="[date]"]').each(function () {
            disabledDates.push($(this).val());
        });

        $scheduleModal.trigger('new-schedule.cs.oyako', [disabledDates]);
    });

    $form.on('submit', function () {
        if ($schedulesWrap.find('.schedule').length === 0) {
            alert('開催日を追加してください。');
            return false;
        }
    });

    /**
     * execute
     */
    function execute() {
        // initialize schedule
        $.each(defaultSchedules, function (i, schedule) {
            var theaters = [];

            $.each(schedule.theaters, function (i, id) {
                var theater = {
                    id: id,
                    name: theaterChoices[id],
                };
                theaters.push(theater);
            });

            $schedulesWrap.trigger('create-schedule.cs.oyako', [schedule.date, theaters]);
        });
    }

    execute();
});
