/**
 * modal/select-title.js
 */
$(function(){
    var $modal = $('#selectTitleModal');
    var titleRowTmpl = $.templates("#selectTitleRowTmpl");
    var titles;
    
    $modal.on('click', '.btn-select', function() {
        var selectTitleId = $(this).data('id');
        
        $(this).trigger('selected.cs.title', [ titles[selectTitleId] ]);
        
        $modal.modal('hide');
    });
    
    $modal.find('.btn-find').click(function(){
        var name = $modal.find('input[name="name"]').val();
        
        if (!name) {
            return;
        }
        
        titles = [];
        
        var $list = $modal.find('tbody.list');
        $list.empty();
        
        var jqXHR = api.title.find(name);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, title) {
                    titles[title.id] = title;
                    
                    $list.append(
                        titleRowTmpl.render(title)
                    );
                });
            })
            .fail(function() {
            });
    });
});
