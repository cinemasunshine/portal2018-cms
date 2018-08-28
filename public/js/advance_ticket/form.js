/**
 * advance_ticket/form.js
 */
$(function(){
    var $form = $('form[name="advance_sale"]');
    
    $.datetimepicker.setLocale('ja');
    
    var datepickerOption = {
        timepicker:false,
        format: 'Y/m/d'
    };
    
    $form.find('.datepicker').datetimepicker(datepickerOption);
    
    var datetimepickerOption = {
        format: 'Y/m/d H:i'
    };
    $form.find('.datetimepicker').datetimepicker(datetimepickerOption); 
    
    $form.find('input[type="checkbox"][name="not_exist_publishing_expected_date"]').change(function(){
        var $publishingExpectedDate = $form.find('input[name="publishing_expected_date"]');
        
        if ($(this).is(':checked')) {
            $publishingExpectedDate.prop('disabled', true);
        } else {
            $publishingExpectedDate.prop('disabled', false);
        }
    });
    
    var $titleField = $form.find('.form-group.title');
    var $tickets = $('#tickets');
    
    var ticketFiledsetTmpl = $.templates("#ticketFiledsetTmpl");
    var filedsetIndex = $tickets.find('.ticket').length;
    
    $('.btn-add-fieldset').click(function() {
        addFieldset();
        toggleFieldsetDeleteBtn();
    });
    
    $tickets.on('click', '.ticket .btn-delete', function() {
        $(this).parents('.ticket').remove();
        toggleFieldsetDeleteBtn();
    });
    
    /**
     * fieldset削除ボタンの切り替え
     * 
     * １件は必須とする。
     */
    function toggleFieldsetDeleteBtn() {
        var $ticket = $tickets.find('.ticket');
        
        if ($ticket.length > 1) {
            $ticket.find('.btn-delete').show();
        } else {
            $ticket.find('.btn-delete').hide();
        }
    }
    
    /**
     * add fieldset
     */
    function addFieldset() {
        var $fieldset = $(ticketFiledsetTmpl.render({
            index: filedsetIndex
        }));
        
        $fieldset.find('.datetimepicker').datetimepicker(datetimepickerOption); 
        $tickets.append($fieldset);
        
        filedsetIndex++;
    }
    
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
    }
    
    /**
     * clear title
     */
    function clearTitle() {
        $titleField.find('input[name="title_id"]').val('');
        $titleField.find('input[name="title_name"]').val('');
        $titleField.find('.title-name').text('選択されていません。');
    }
    
    /**
     * execute
     */
    function execute() {
        if ($tickets.find('.ticket').length === 0) {
            addFieldset();
        }
        
        toggleFieldsetDeleteBtn();
        
        var id = $('input[name="title_id"]').val();
        
        if (!id) {
            clearTitle();
        }
        
        $form.find('input[type="checkbox"][name="not_exist_publishing_expected_date"]').change();
    }
    
    execute();
});