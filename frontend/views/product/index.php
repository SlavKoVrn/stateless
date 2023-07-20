<?php

use common\models\Category;
use common\models\Tag;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
\common\assets\DataTableAsset::register($this);
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'category_id')->dropDownList(array_merge([0=>''],Category::getCategories())) ?>
<?= $form->field($model, 'tags')->dropDownList(Tag::getAllArray(),['multiple' => true]) ?>
<div class="form-group">
    <?= Html::submitButton('Поиск', ['id'=>'search_product','class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>

<ul class="pagination"></ul>
<table id="myTable" class="display">
    <thead>
        <tr>
            <th></th>
            <th>Ид</th>
            <th>Категория</th>
            <th>Название</th>
            <th>Тэги</th>
            <th>Цена</th>
        </tr>
    </thead>
</table>
<ul class="pagination"></ul>
<style>
    ul.pagination > li {
        padding:12px;
    }
    ul.pagination > li.active {
        color:white;
        background-color:lightgrey;
    }
    table td {
        vertical-align: top;
    }
</style>
<?php
$js=<<<JS
    $(document).on('click','#search_product',function(e){
        e.preventDefault();
        $.ajax({
            type:'get',
            url: '/api/product',
            data:{
                's[name]':$('#product-name').val(),
                's[category_id]':$('#product-category_id option:selected').val(),
                's[tags]':$('#product-tags').val(),
            },
            success: function(data, status, jqXHR) {
                var current_page = jqXHR.getResponseHeader('x-pagination-current-page');
                var page_count   = jqXHR.getResponseHeader('x-pagination-page-count');
                var per_page     = jqXHR.getResponseHeader('x-pagination-per-page');
                var total_count  = jqXHR.getResponseHeader('x-pagination-total-count');
                $('.pagination').html('');
                window.table(data,per_page,true);
            },
            error: function () {
            },
        });
    });
    const detailRows = [];
    function description(data) {
        return data.description;
    }
    $(document).on('click','ul.pagination li',function(e){
        e.preventDefault();
        window.pagination($(this).data('page'));
    });
    window.table = function(data,per_page,paging){
        if ( $.fn.dataTable.isDataTable( '#myTable' ) ) {
            $('#myTable').DataTable().clear().draw();
            $('#myTable').DataTable().rows.add(data).draw();
        }else{
            $('#myTable').DataTable({
                data:data,
                'pageLength': per_page,
                'paging':paging,
                'searching':false,
                columns: [
                    {
                        class: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    { data: 'id' },
                    { 
                        data: 'category',
                        'render':function(data,type,row){
                            return data.id+'. '+data.name;
                        }
                    },
                    { data: 'name' },
                    {
                        data: 'tags',
                        'render': function (data, type, row) {
                            let tags = '<table>';
                            data.forEach(function(tag){
                                tags+='<tr><td>'+tag.id+'. '+tag.name+'</td></tr>';
                            });
                            return tags;
                        }
                    },
                    { data: 'price' },
                ]
            });
            
            $('#myTable').on('click', 'tbody td.dt-control', function () {
                let tr = event.target.closest('tr');
                let row = $('#myTable').DataTable().row(tr);
                let idx = detailRows.indexOf(tr.id);
             
                if (row.child.isShown()) {
                    tr.classList.remove('details');
                    row.child.hide();
             
                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                }
                else {
                    tr.classList.add('details');
                    row.child(description(row.data())).show();
             
                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.id);
                    }
                }
            });
             
            // On each draw, loop over the `detailRows` array and show any child rows
            $('#myTable').on('draw', () => {
                detailRows.forEach((id, i) => {
                    let el = document.querySelector('#' + id + ' td.dt-control');
             
                    if (el) {
                        el.dispatchEvent(new Event('click', { bubbles: true }));
                    }
                });
            });
        }
    }
    window.pagination = function(page){
        $.ajax({
            type:'get',
            url: '/api/product',
            data:{
                'page':page
            },
            success: function(data, status, jqXHR) {
                var current_page = jqXHR.getResponseHeader('x-pagination-current-page');
                var page_count   = jqXHR.getResponseHeader('x-pagination-page-count');
                var per_page     = jqXHR.getResponseHeader('x-pagination-per-page');
                var total_count  = jqXHR.getResponseHeader('x-pagination-total-count');
                var paginationHtml = '';
                for (var i = 1; i <= page_count; i++) {
                    if (i === Number(current_page)) {
                        paginationHtml += '<li class="active" data-page="' + i + '">';
                    } else {
                        paginationHtml += '<li data-page="' + i + '">';
                    }
                    paginationHtml += '<a href="/api/product?page=' + i + '">' + i + '</a></li>';
                }
                $('.pagination').html(paginationHtml);
                window.table(data,per_page,false);
            },
            error: function () {
            },
        });
    }
    window.pagination(1);
JS;
$this->registerJS($js);