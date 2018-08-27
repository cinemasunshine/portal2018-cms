/**
 * news/publication.js
 */
$(function(){
    var sortableOptions = {
        filter: '.btn-delete',
        onFilter: function(event) {
            var $item = $(event.item);
            
            if (Sortable.utils.is(event.target, '.btn-delete')) {
                var $list = $item.parents('.list-group');
                $item.remove();
                resetDisplayOrder($list);
            }
        },
        onUpdate: function(event) {
            var $item = $(event.item);
            resetDisplayOrder($item.parents('.list-group'));
        }
    };
    
    $('.sortable').each(function() {
        Sortable.create($(this).get(0), sortableOptions);
    });
    
    var $addTargetList;
    var addIndex = 0;
    
    var newsRowTmpl = $.templates("#newsRowTmpl");
    var $selectNewsModal = $('#selectNewsModal');
    
    $selectNewsModal.on('show.bs.modal', function(event) {
        var $button = $(event.relatedTarget);
        
        $addTargetList = $button.closest('.card').find('.list-group');
    });
    
    $selectNewsModal.on('selected.cs.news', function(event, newsList) {
        $.each(newsList, function(i, news) {
            var data = news;
            data.index = 'add' + addIndex;
            $addTargetList.append(newsRowTmpl.render(data));
            
            addIndex++;
        });
        
        resetDisplayOrder($addTargetList);
    });
    
    /**
     * reset display_order
     * 
     * @param {String} $list
     */
    function resetDisplayOrder($list) {
        var $inputDisplayOrder = $list.find('.list-group-item input[name*="display_order"]');
        var displayOrder = 1;
        
        $inputDisplayOrder.each(function() {
            $(this).val(displayOrder);
            displayOrder++;
        });
    }
});
