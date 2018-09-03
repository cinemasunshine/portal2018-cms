/**
 * modal/select-title-master.js
 */
$(function(){
    var $modal = $('#selectTitleMasterModal');
    var $form = $modal.find('form');
    var $list = $modal.find('tbody.list');
    
    var titleRowTmpl = $.templates('#selectTitleMasterRowTmpl');
    
    $modal.find('.btn-find').click(function() {
        // validation
        $form.addClass('was-validated');
        
        if ($form.find(':invalid').length > 0) {
            return;
        }
        
        var theater = $form.find('select[name="theater"]').val();
        var name = $form.find('input[name="name"]').val();
        
        // initialize
        titles = [];
        
        $list.empty();
        
        // find
        var jqXHR = api.title.master(theater, name);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, title) {
                    titles[title.code] = title;
                    
                    $list.append(
                        titleRowTmpl.render(title)
                    );
                });
            })
            .fail(function() {
            });
    });
    
    $modal.on('click', '.btn-select', function() {
        var selectTitleCode = $(this).data('code');
        
        $(this).trigger('selected.cs.title_master', [ titles[selectTitleCode] ]);
        
        $modal.modal('hide');
    });
});
