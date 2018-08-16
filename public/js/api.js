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
});