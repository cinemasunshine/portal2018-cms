/**
 * trailer/form.js
 */
$(function(){
    var $form = $('form[name="trailer"]');
    var $titleField = $form.find('.form-group.field-title');
    
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
     * @param {Array} title
     */
    function setTitle(title) {
        $titleField.find('input[name="title_id"]').val(title.id);
        $titleField.find('input[name="title_name"]').val(title.name);
        $titleField.find('.title-name').text(title.name);
        $titleField.find('.btn-clear').show();
        
        $form.find('input[name="banner_link_url"]').val(title.official_site);
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
        var id = $('input[name="title_id"]').val();
        
        if (!id) {
            clearTitle();
        }
    }
    
    execute();
});
