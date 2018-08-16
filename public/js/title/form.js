/**
 * title/form.js
 */
$(function(){
    $.datetimepicker.setLocale('ja');
    var datetimepickerOption = {
        timepicker:false,
        format: 'Y/m/d'
    };
    
    $('.datetimepicker').datetimepicker(datetimepickerOption);
    
    $('input[name="not_exist_publishing_expected_date"]').change(function(){
        var $publishingExpectedDate = $('input[name="publishing_expected_date"]');
        
        if ($(this).is(':checked')) {
            $publishingExpectedDate.prop('disabled', true);
        } else {
            $publishingExpectedDate.prop('disabled', false);
        }
    });
    
    function execute() {
        $('input[name="not_exist_publishing_expected_date"]').change();
    }
    
    execute();
});