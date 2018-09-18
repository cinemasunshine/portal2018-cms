/**
 * campaign/form.js
 */
$(function(){
    var $form = $('form[name="campaign"]');
    
    $form.find('.datetimepicker').datetimepicker(datetimepickerOption);
    
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
        $titleField.find('input[name="title_name"]').val(title.name);
        $titleField.find('.title-name').text(title.name);
        $titleField.find('.btn-clear').show();
    }
    
    /**
     * clear title
     */
    function clearTitle() {
        $titleField.find('input[name="title_id"]').val('');
        $titleField.find('input[name="title_name"]').val('');
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