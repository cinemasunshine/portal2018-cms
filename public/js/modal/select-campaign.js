/**
 * modal/select-campaign.js
 */
$(function(){
    var $modal = $('#selectCampaignModal');
    var campaignRowTmpl = $.templates("#selectCampaignRowTmpl");
    var campaigns;
    
    $modal.find('.btn-find').click(function(){
        var name = $modal.find('input[name="name"]').val();
        
        if (!name) {
            return;
        }
        
        formDisable();
        
        var $list = $modal.find('tbody.list');
        $list.empty();
        
        campaigns = [];
        
        var jqXHR = api.campaign.find(name);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, campaign) {
                    campaigns[campaign.id] = campaign;
                    
                    $list.append(
                        campaignRowTmpl.render(campaign)
                    );
                });
            })
            .fail(function() {
            })
            .always(function() {
                formEnable();
            });
    });
    
    /**
     * form disable
     */
    function formDisable() {
        var $form = $modal.find('.form');
        $form.find('input, button').prop('disabled', true);
    }
    
    /**
     * form enable
     */
    function formEnable() {
        var $form = $modal.find('.form');
        $form.find('input, button').prop('disabled', false);
    }
    
    $modal.on('click', '.btn-select', function() {
        var $selected = $modal.find('input[name="campaign[]"]:checked');
        var selectedCampaigns = [];
        
        $selected.each(function() {
            selectedCampaigns.push(campaigns[$(this).val()]);
        });
        
        $(this).trigger('selected.cs.campaign', [ selectedCampaigns ]);
        
        $modal.modal('hide');
    });
});
