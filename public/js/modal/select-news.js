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
            });
    });
    
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