/**
 * modal/select-main-banner.js
 */
$(function(){
    var $modal = $('#selectMainBannerModal');
    var mainBannerRowTmpl = $.templates("#selectMainBannerRowTmpl");
    var mainBannerList;
    
    $modal.find('.btn-find').click(function(){
        var name = $modal.find('input[name="name"]').val();
        
        if (!name) {
            return;
        }
        
        var $list = $modal.find('tbody.list');
        $list.empty();
        
        mainBannerList = [];
        
        var jqXHR = api.mainBanner.find(name);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, mainBanner) {
                    mainBannerList[mainBanner.id] = mainBanner;
                    
                    $list.append(
                        mainBannerRowTmpl.render(mainBanner)
                    );
                });
            })
            .fail(function() {
            });
    });
    
    $modal.on('click', '.btn-select', function() {
        var $selected = $modal.find('input[name="main_banner[]"]:checked');
        var selectedList = [];
        
        $selected.each(function() {
            selectedList.push(mainBannerList[$(this).val()]);
        });
        
        $(this).trigger('selected.cs.main_banner', [ selectedList ]);
        
        $modal.modal('hide');
    });
});
