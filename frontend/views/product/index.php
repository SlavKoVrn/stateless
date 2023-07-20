<?php
/** @var yii\web\View $this */
\common\assets\DataTableAsset::register($this);
?>
<ul class="pagination"></ul>
<table id="myTable" class="display">
    <thead>
        <tr>
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
    $(document).on('click','ul.pagination li',function(e){
        e.preventDefault();
        window.pagination($(this).data('page'));
    });
    window.pagination = function(page){
        $.ajax({
            type:'get',
            url: '/api/product',
            data:{
                'page':page
            },
            success: function(data, status, jqXHR) {
                console.log(data);
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
                if ( $.fn.dataTable.isDataTable( '#myTable' ) ) {
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().rows.add(data).draw();
                }else{
                    $('#myTable').DataTable({
                        data:data,
                        'pageLength': per_page,
                        'paging':false,
                        'searching':false,
                        columns: [
                            { data: 'id' },
                            { data: 'category.name' },
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
                }
            },
            error: function () {
            },
        });
    }
    window.pagination(1);
JS;
$this->registerJS($js);