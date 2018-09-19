/**
 * schedule/form.js
 */
$(function(){
    var $form = $('form[name="schedule"]');
    
    $form.find('.datepicker').datetimepicker(datepickerOption);
    $form.find('.datetimepicker').datetimepicker(datetimepickerOption); 
    
    var $titleField = $form.find('.form-group.title');
    var $selectTitleModal = $('#selectTitleModal');
    
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
    
    $form.find('input[name="public_end_dt"]').change(function() {
        var value = $(this).val();
        
        if (value) {
            var date = new Date(value);
            value = date.getFullYear()
                  + '/'
                  + ('00' + (date.getMonth() + 1)).slice(-2)
                  + '/'
                  + ('00' + date.getDate()).slice(-2);
        }
        
        $form.find('input[name="end_date"]').val(value);
    });
    
    var $formats = $('#formats');
    var formatFiledsetIndex = $formats.find('.format').length;
    var formatFiledsetTmpl = $.templates('#formatFiledsetTmpl');
    
    $formats.on('click', '.format .btn-delete', function() {
        var $format = $(this).closest('.format');
        
        $format.remove();
        controlFormatFieldsetDeleteBtn();
    });
    
    $('.btn-add-format-fieldset').click(function() {
        addFormatFieldset();
        controlFormatFieldsetDeleteBtn();
    });
    
    /**
     * add format fieldset
     */
    function addFormatFieldset() {
        var $fieldset = $(formatFiledsetTmpl.render({
            index: formatFiledsetIndex
        }));
        
        $formats.append($fieldset);
        
        formatFiledsetIndex++;
    }
    
    /**
     * 上映方式fieldset削除ボタンの切り替え
     * 
     * １件は必須とする。
     */
    function controlFormatFieldsetDeleteBtn() {
        var $format = $formats.find('.format');
        
        if ($format.length > 1) {
            $format.find('.btn-delete').show();
        } else {
            $format.find('.btn-delete').hide();
        }
    }
    
    $form.find('.btn-check-all').click(function(e) {
        $(this).closest('.form-group').find(':checkbox').prop('checked', true);
    });
    
    $form.find('.btn-uncheck-all').click(function() {
        $(this).closest('.form-group').find(':checkbox').prop('checked', false);
    });
    
    /**
     * execute
     */
    function execute() {
        if ($formats.find('.format').length === 0) {
            addFormatFieldset();
        }
        
        controlFormatFieldsetDeleteBtn();
    }
    
    execute();
});
