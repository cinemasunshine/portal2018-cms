/**
 * main_banner/publication.js
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
    
    var mainBannerRowTmpl = $.templates("#mainBannerRowTmpl");
    var $selectMainBannerModal = $('#selectMainBannerModal');
    
    $selectMainBannerModal.on('show.bs.modal', function(event) {
        var $button = $(event.relatedTarget);
        
        $addTargetList = $button.closest('.card').find('.list-group');
    });
    
    $selectMainBannerModal.on('selected.cs.main_banner', function(event, mainBannerList) {
        $.each(mainBannerList, function(i, mainBanner) {
            var data = mainBanner;
            data.index = 'add' + addIndex;
            $addTargetList.append(mainBannerRowTmpl.render(data));
            
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