/**
 * theater_meta/opening_hour/form.js
 */
$(function(){
    var $form = $('form[name="opening_hour"]');
    
    // javascriptで動的に追加された項目については別途設定する
    $form.find('.datepicker').datetimepicker(datepickerOption);
    $form.find('.timepicker').datetimepicker(timepickerOption);
    
    var $hours = $('#hours');
    var fieldsetTmpl = $.templates("#filedsetTmpl");
    var filedsetIndex = $hours.find('.hour').length;
    
    $('.btn-add-fieldset').click(function() {
        addFieldset();
    });
    
    /**
     * add fieldset
     */
    function addFieldset() {
        var $fieldset = $(fieldsetTmpl.render({
            index: filedsetIndex
        }));
        
        $fieldset.find('.datepicker').datetimepicker(datepickerOption);
        $fieldset.find('.timepicker').datetimepicker(timepickerOption);
        
        $hours.append($fieldset);
        
        $fieldset.find('input[name*="[type]"]:eq(0)')
            .prop('checked', true)
            .change();
        
        filedsetIndex++;
    }
    
    $hours.on('change', 'input[name*="[type]"]', function() {
        var $dateField = $(this).closest('.hour').find('.field-date');
        
        if ($(this).val() === '1') {
            $dateField.find('input[name*="[to_date]"]')
                .prop('disabled', true)
                .hide();
            $dateField.find('.input-group-addon').hide();
        } else {
            $dateField.find('input[name*="[to_date]"]')
                .prop('disabled', false)
                .show();
            $dateField.find('.input-group-addon').show();
        }
    });
    
    $hours.on('click', '.hour .btn-delete-fieldset', function() {
        $(this).closest('.hour').remove();
    });
    
    /**
     * execute
     */
    function execute() {
        $hours.find('input[name*="[type]"]:checked').change();
    }
    
    execute();
});
