/**
 * modal/select-news.js
 */
$(function(){
    var $modal = $('#selectNewsModal');
    var newsRowTmpl = $.templates("#selectNewsRowTmpl");
    var newsList;
    
    $modal.find('.btn-find').click(function(){
        var headline = $modal.find('input[name="headline"]').val();
        
        if (!headline) {
            return;
        }
        
        formDisable();
        
        var $list = $modal.find('tbody.list');
        $list.empty();
        
        newsList = [];
        
        var jqXHR = api.news.find(headline);
        jqXHR
            .done(function(data) {
                $.each(data.data, function(i, news) {
                    newsList[news.id] = news;
                    
                    $list.append(
                        newsRowTmpl.render(news)
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
        var $selected = $modal.find('input[name="news[]"]:checked');
        var selectedNewsList = [];
        
        $selected.each(function() {
            selectedNewsList.push(newsList[$(this).val()]);
        });
        
        $(this).trigger('selected.cs.news', [ selectedNewsList ]);
        
        $modal.modal('hide');
    });
});