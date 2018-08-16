/**
 * modal/select-title.js
 */
$(function(){
    var $modal = $('#selectTitleModal');
    var titleRowTmpl = $.templates("#selectTitleRowTmpl");
    
    $modal.on('click', '.btn-select', function() {
        var $titleRow = $(this).parents('.title');
        
        $(this).trigger('selected.cs.title', [
            $titleRow.data('id'),
            $titleRow.data('name')
        ]);
        
        $modal.modal('hide');
    });
    
    $modal.find('.btn-find').click(function(){
        var name = $modal.find('input[name="name"]').val();
        
        if (!name) {
            return;
        }
        
        var $list = $modal.find('tbody.list');
        $list.empty();
        
        var jqXHR = api.title.find(name);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, title) {
                    $list.append(
                        titleRowTmpl.render(title)
                    );
                });
            })
            .fail(function() {
            });
    });
});
