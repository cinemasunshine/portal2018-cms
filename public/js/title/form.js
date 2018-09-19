/**
 * title/form.js
 */
$(function(){
    var $form = $('form[name="title"]');
    
    $form.find('.datepicker').datetimepicker(datepickerOption);
    
    $form.find('input[name="not_exist_publishing_expected_date"]').change(function(){
        var $publishingExpectedDate = $form.find('input[name="publishing_expected_date"]');
        
        if ($(this).is(':checked')) {
            $publishingExpectedDate.prop('disabled', true);
        } else {
            $publishingExpectedDate.prop('disabled', false);
        }
    });
    
    /**
     * execute
     */
    function execute() {
        $form.find('input[name="not_exist_publishing_expected_date"]').change();
    }
    
    execute();
});