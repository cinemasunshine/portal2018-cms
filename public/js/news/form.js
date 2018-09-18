/**
 * news/form.js
 */
$(function(){
    var $form = $('form[name="news"]');
    
    $form.find('.datetimepicker').datetimepicker(datetimepickerOption);
    
    $.extend(editorOptions, {
        height: 200,
        callbacks: {
            onImageUpload: function(files) {
                $.each(files, function(i, file) {
                    upload(file);
                });
            }
        }
    });
    
    var $summernote = $form.find('textarea.editor').summernote(editorOptions);
    
    /**
     * upload from editor
     * 
     * @param {File} file
     * @returns {jqXHR}
     */
    function upload(file) {
        var jqXHR = api.editor.upload(file);
        jqXHR
            .done(function(data) {
                if (data.errors) {
                    var message = '';
                    
                    $.each(data.errors, function(i, error) {
                        if (i > 0) {
                            message += "\n";
                        }
                        
                        message += error.title;
                    });
                    
                    alert(message);
                    return;
                }
                
                var file = data.data;
                $summernote.summernote('insertImage', file.url);
            })
            .fail(function() {
            });
    }
    
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
     * @param {Array} title
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
