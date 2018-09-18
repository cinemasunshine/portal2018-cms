/**
 * news/list.js
 */
$(function(){
    var $form = $('form[name="find_news"]');
    
    $form.find('.btn-check-all').click(function(e) {
        $(this).closest('.form-group').find(':checkbox').prop('checked', true);
    });
    
    $form.find('.btn-uncheck-all').click(function() {
        $(this).closest('.form-group').find(':checkbox').prop('checked', false);
    });
});
