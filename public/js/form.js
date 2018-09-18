/**
 * form.js
 */

var datetimepickerOption;
var datepickerOption;
var timepickerOption;
var editorOptions;

$(function(){
    if ($.datetimepicker) {
        $.datetimepicker.setLocale('ja');
    }
    
    datetimepickerOption = {
        format: 'Y/m/d H:i'
    };
    
    datepickerOption = {
        timepicker: false,
        format: 'Y/m/d'
    };
    
    timepickerOption = {
        datepicker: false,
        format: 'H:i'
    };
    
    editorOptions = {
        lang: 'ja-JP',
        toolbar: [
            ['edit', ['undo', 'redo']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'link', 'table', 'hr']],
            ['etc', ['fullscreen', 'codeview', 'help']]
        ]
    };
});
