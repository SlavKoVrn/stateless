<?php
namespace backend\modules\news\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class BulkButtonWidget extends InputWidget
{
    public $pjaxId;
    public $gridId;
    public $name = 'bulkDelete';

    public function run(): string
    {
        $this->registerScript();

        return Html::button('Удалить выделенные', ['id' => 'delete_checked', 'class' => 'btn btn-danger']);
    }

    private function registerScript(): void
    {
        $view = $this->getView();

        $js =<<<JS
$(document).on('click', '#{$this->gridId} .checkbox', function(e) {
    e.stopPropagation();
    var id = $(this).data('id');
    if ($(this).is(':checked')) {
        if (!window.checkedIds.includes(id)) {
            window.checkedIds.push(id);
        }
    } else {
        var index = window.checkedIds.indexOf(id);
        if (index > -1) {
            window.checkedIds.splice(index, 1);
        }
    }
});
$(document).on('click', '#delete_checked', function(e){
    e.preventDefault();
    e.stopPropagation();
    if (confirm('Удалить выбранное ?')){
        $.ajax({
            url: '/admin/news/bulk',
            method: 'post',
            dataType: 'json',
            data: {ids: window.checkedIds},
            success: function(data){
                if (data.deleted){
                    $.pjax.reload({container: '#pjax-grid',timeout: 0});
                }
            }
        });
    }
});
window.performAfterPjax = function(){
    window.checkedIds = [];
    $('#{$this->gridId} thead tr').each(function() {
        var td = $('<td>');
        $(this).prepend(td);
    });
    $('#{$this->gridId} tbody tr').each(function() {
        var key = $(this).data('key');
        var checkboxTd = $('<td>');
        var checkboxInput = $('<input>', {
            type: 'checkbox',
            class: 'checkbox',
            'data-id': key
        });
        checkboxTd.append(checkboxInput);
        $(this).prepend(checkboxTd);
    });
};
$(document).on('pjax:complete', function(event) {
    window.performAfterPjax();
});
window.performAfterPjax();
JS;
        $view->registerJs($js);
    }

}
