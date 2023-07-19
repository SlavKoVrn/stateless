<?php
/** @var yii\web\View $this */
\common\assets\DataTableAsset::register($this);
?>
<table id="myTable" class="display">
    <thead>
        <tr>
            <th>Ид</th>
            <th>Категория</th>
            <th>Название</th>
            <th>Описание</th>
        </tr>
    </thead>
</table>
<?php
$js=<<<JS
    $.ajax({
        type:'get',
        url: '/api/product',
        success: function (data) {
            $('#myTable').DataTable({
                data:data,
                columns: [
                    { data: 'id' },
                    { data: 'category.name' },
                    { data: 'name' },
                    { data: 'description' },
                ]
            });
        },
        error: function () {
        },
    });
JS;
$this->registerJS($js);