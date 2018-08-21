/**
 * campaign/form.js
 */
$(function(){
    $.datetimepicker.setLocale('ja');
    var datetimepickerOption = {
        format: 'Y/m/d H:i'
    };
    
    $('.datetimepicker').datetimepicker(datetimepickerOption);
    
    var $form = $('form[name="campaign"]');
    var $titleField = $form.find('.form-group.title');
    
    $titleField.find('.btn-clear').click(function() {
       clearTitle(); 
    });
    
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
        $titleField.find('.title-name').text(title.name);
        $titleField.find('.btn-clear').show();
    }
    
    /**
     * clear title
     */
    function clearTitle() {
        $titleField.find('input[name="title_id"]').val('');
        $titleField.find('.title-name').text('選択されていません。');
        $titleField.find('.btn-clear').hide();
    }
    
    /**
     * execute
     */
    function execute() {
        var id = $('input[name="title_id"]').val();
        
        if (!id) {
            clearTitle();
        }
    }
    
    execute();
});