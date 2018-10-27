/**
 * common.js
 */

$(function(){
    /**
     * 削除アクション用 確認ダイアログ
     */
    $(document).on('click', '.confirm-delete', function() {
        return confirm('削除します。よろしいですか？');
    });
});