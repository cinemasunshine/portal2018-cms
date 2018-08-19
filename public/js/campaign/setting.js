/**
 * campaign/setting.js
 */
$(function() {
    var sortableOptions = {
        filter: '.btn-delete',
        onFilter: function(event) {
            var $item = $(event.item);
            
            if (Sortable.utils.is(event.target, '.btn-delete')) {
                $item.remove();
                resetDisplayOrder($item.parents('.list-group'));
            }
        },
        onUpdate: function() {
            var $item = $(event.item);
            resetDisplayOrder($item.parents('.list-group'));
        }
    };
    
    $('.sortable').each(function() {
        Sortable.create($(this).get(0), sortableOptions);
    });
    
    var $addTargetList;
    
    var campaignRowTmpl = $.templates("#campaignRowTmpl");
    var $selectCampaignModal = $('#selectCampaignModal');
    
    $selectCampaignModal.on('show.bs.modal', function(event) {
        var $button = $(event.relatedTarget);
        
        $addTargetList = $button.parents('.card').find('.list-group');
    });
    
    $selectCampaignModal.on('selected.cs.campaign', function(event, campaigns) {
        $.each(campaigns, function(i, campaign) {
            $addTargetList.append(campaignRowTmpl.render(campaign));
        });
        
        resetDisplayOrder($addTargetList);
    });
    
    /**
     * reset display_order
     * 
     * @param {String} $list
     */
    function resetDisplayOrder($list) {
        var $campaignId = $list.find('input.campaing-id');
        var displayOrder = 1;
        
        $campaignId.each(function() {
            $(this).attr('name', 'campaign[' + displayOrder + ']');
            displayOrder++;
        });
    }
});
