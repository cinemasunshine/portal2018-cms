/**
 * main_banner/form.js
 */
$(function(){
    var $form = $('form[name="main_banner"]');
    
    $form.find('input[name="link_type"]').change(function() {
        controlLinkUrlField($(this).val());
    });
    
    /**
     * リンクURLフィールド制御
     */
    function controlLinkUrlField(type) {
        var $fieldLinkUrl = $form.find('.field-link-url');
        
        if (type === '2') {
            $fieldLinkUrl.show();
            $fieldLinkUrl.find('input').prop('disabled', false);
        } else {
            $fieldLinkUrl.find('input').prop('disabled', true);
            $fieldLinkUrl.hide();
        }
    }
    
    /**
     * execute
     */
    function execute() {
        controlLinkUrlField($form.find('input[name="link_type"]:checked').val());
    }
    
    execute();
});
