/**
 * form.js
 */

var datetimepickerOption;
var datepickerOption;
var timepickerOption;
var editorOptions;

$(function(){
    if ($.datetimepicker) {
        initDatetimepicker();
    }
    
    /**
     * initialize datetimepicker
     */
    function initDatetimepicker() {
        $.datetimepicker.setLocale('ja');
        
        var baseOption = {
            scrollMonth: false,
            scrollTime: false,
            scrollInput: false
        };
        
        datetimepickerOption = $.extend({}, baseOption, {
            format: 'Y/m/d H:i'
        });
        
        datepickerOption = $.extend({}, baseOption, {
            timepicker: false,
            format: 'Y/m/d'
        });
        
        timepickerOption = $.extend({}, baseOption, {
            datepicker: false,
            format: 'H:i'
        });
    }
    
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
