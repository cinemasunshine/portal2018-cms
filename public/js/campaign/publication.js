/**
 * campaign/publication.js
 */
$(function() {
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
    
    var campaignRowTmpl = $.templates("#campaignRowTmpl");
    var $selectCampaignModal = $('#selectCampaignModal');
    
    $selectCampaignModal.on('show.bs.modal', function(event) {
        var $button = $(event.relatedTarget);
        
        $addTargetList = $button.closest('.card').find('.list-group');
    });
    
    $selectCampaignModal.on('selected.cs.campaign', function(event, campaigns) {
        $.each(campaigns, function(i, campaign) {
            var data = campaign;
            data.index = 'add' + addIndex;
            $addTargetList.append(campaignRowTmpl.render(data));
            
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
