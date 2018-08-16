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
    
    $selectTitleModal.on('selected.cs.title', function(event, id, name) {
        setTitle(id, name);
    });
    
    /**
     * set title
     * 
     * @param {Integer} id
     * @param {String}  name
     */
    function setTitle(id, name) {
        $titleField.find('input[name="title_id"]').val(id);
        $titleField.find('.title-name').text(name);
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