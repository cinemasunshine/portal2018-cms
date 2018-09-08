/**
 * API
 */
var api;

$(function(){
    api = {};
    api.title = {};
    
    /**
     * find title
     * 
     * @param {String} name
     * @returns {jqXHR}
     */
    api.title.find = function(name) {
        return $.ajax({
            url: '/api/title/list',
            data: {
                'name': name
            }
        });
    };
    
    api.campaign = {};
    
    /**
     * find campaign
     * 
     * @param {String} name
     * @returns {jqXHR}
     */
    api.campaign.find = function(name) {
        return $.ajax({
            url: '/api/campaign/list',
            data: {
                'name': name
            }
        });
    };
    
    api.mainBanner = {};
    
    /**
     * find main_banner
     * 
     * @param {String} name
     * @returns {jqXHR}
     */
    api.mainBanner.find = function(name) {
        return $.ajax({
            url: '/api/main_banner/list',
            data: {
                name: name
            }
        });
    };
    
    api.news = {};
    
    /**
     * find news
     * 
     * @param {String} headline
     * @returns {jqXHR}
     */
    api.news.find = function(headline) {
        return $.ajax({
            url: '/api/news/list',
            data: {
                headline: headline
            }
        });
    };
    
    api.editor = {};
    
    /**
     * upload from editor
     * 
     * @param {File} file
     * @returns {jqXHR}
     */
    api.editor.upload = function(file) {
        var data = new FormData();
        data.append('file', file, file.name);
        
        return $.ajax({
            method: 'POST',
            url: '/api/editor/upload',
            processData: false,
            contentType: false,
            dataType: 'JSON',
            data: data
        });
    }
});