/**
 * title_ranking/form.js
 */
$(function(){
    var $form = $('form[name="title_ranking"]');
    
    $form.find('.datepicker').datetimepicker(datepickerOption);
    
    var $selectTitleModal = $('#selectTitleModal');
    var $ranks = $form.find('#ranks');
    var $targetRank;
    
    $selectTitleModal.on('show.bs.modal', function(event) {
        var $button = $(event.relatedTarget);
        
        $targetRank = $button.closest('.rank');
    });
    
    $form.find('.rank .field-title .btn-clear').click(function() {
        var $field = $(this).closest('.field-title');
        clearTitle($field); 
    });
    
    $selectTitleModal.on('selected.cs.title', function(event, title) {
        setTitle(title);
    });
    
    /**
     * set title
     * 
     * @param {Object} title
     */
    function setTitle(title) {
        var $field = $targetRank.find('.field-title');
        $field.find('input[name$="[title_id]"]').val(title.id);
        $field.find('input[name$="[title_name]"]').val(title.name);
        $field.find('.title-name').text(title.name);
        $field.find('.btn-clear').show();
    }
    
    /**
     * clear title
     * 
     * @param {jQuery} $field
     */
    function clearTitle($field) {
        $field.find('input[name$="[title_id]"]').val('');
        $field.find('input[name$="[title_name]"]').val('');
        $field.find('.title-name').text('選択されていません。');
        $field.find('.btn-clear').hide();
    }
    
    /**
     * execute
     */
    function execute() {
        $ranks.find('.rank').each(function() {
            var id = $(this).find('input[name$="[title_id]"]').val();
            
            if (!id) {
                clearTitle($(this).find('.field-title'));
            }
        });
    }
    
    execute();
});
